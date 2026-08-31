<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectDetailResource;
use App\Http\Resources\ProjectSummaryResource;
use App\Models\InstallationType;
use App\Models\Location;
use App\Models\PageSection;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioProjectController extends Controller
{
    private const MAX_PER_PAGE = 48;

    /** Eager loads every project payload needs. */
    private const RELATIONS = [
        'media',
        'category:id,name,slug',
        'location:id,name,slug,country',
        'sectors:id,name,slug',
        'installationTypes:id,name,slug',
    ];

    // ─────────────────────────────────────────────────────────────
    //  v1
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/pages/portfolio — the listing screen: hero copy, the filter
     * options, and the first page of results.
     */
    public function page(Request $request): JsonResponse
    {
        $section = PageSection::where('section_key', 'portfolio')->first();
        $result  = $this->buildIndex($request);

        return response()->json([
            'data' => [
                'section' => [
                    'title'       => $section?->title,
                    'subtitle'    => $section?->subtitle,
                    'description' => $section?->description,
                    'cta_label'   => $section?->cta_label,
                    'cta_url'     => $section?->cta_url,
                ],
                // Everything the filter bar needs, so it renders without a
                // second request. Only published taxonomies appear.
                'filters' => [
                    'categories'         => $this->filterOptions(PortfolioCategory::class),
                    'installation_types' => $this->filterOptions(InstallationType::class),
                    'locations'          => Location::orderBy('name')->get(['name', 'slug', 'country'])
                        ->map(fn ($l) => ['name' => $l->name, 'slug' => $l->slug, 'country' => $l->country]),
                    'sectors'            => $this->filterOptions(Sector::class),
                ],
                'projects' => $result['data'],
            ],
            'meta' => $result['meta'],
        ]);
    }

    /** GET /api/v1/projects — filtered, paginated entity list. */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->buildIndex($request));
    }

    /** GET /api/v1/projects/{slug} — the project detail screen. */
    public function show(string $slug): JsonResponse
    {
        return response()->json($this->buildShow($slug));
    }

    // ─────────────────────────────────────────────────────────────
    //  Legacy — /api/portfolio, unenveloped. Frozen shape.
    //  Remove once the frontend moves to /api/v1.
    // ─────────────────────────────────────────────────────────────

    public function legacyIndex(Request $request): JsonResponse
    {
        $query = PortfolioProject::query()
            ->where('status', 'published')
            ->with([...self::RELATIONS, 'productVariants:id,product_id,name,slug', 'productVariants.product:id,name,slug']);

        $this->applyFilters($query, $request);

        return response()->json(
            $query->orderByDesc('created_at')->get()->map(fn ($p) => $this->legacyShape($p))
        );
    }

    public function legacyShow(string $slug): JsonResponse
    {
        $project = PortfolioProject::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with([...self::RELATIONS, 'productVariants:id,product_id,name,slug', 'productVariants.product:id,name,slug'])
            ->firstOrFail();

        return response()->json($this->legacyShape($project, detail: true));
    }

    /**
     * The pre-v1 response shape, reproduced exactly. `category` and the
     * taxonomy arrays are flat strings here, not objects — changing them
     * would break anything already consuming these URLs.
     */
    private function legacyShape(PortfolioProject $project, bool $detail = false): array
    {
        $shape = [
            'id'                 => $project->id,
            'title'              => $project->title,
            'slug'               => $project->slug,
            'cover_image'        => $project->getFirstMediaUrl('cover_image'),
            'category'           => $project->category?->name,
            'location'           => $project->location ? [
                'name'    => $project->location->name,
                'country' => $project->location->country,
            ] : null,
            'sectors'            => $project->sectors->pluck('name'),
            'installation_types' => $project->installationTypes->pluck('name'),
            'product_variants'   => $project->productVariants->map(fn ($v) => [
                'id'      => $v->id,
                'name'    => $v->name,
                'slug'    => $v->slug,
                'product' => $v->product?->name,
            ]),
        ];

        if ($detail) {
            $shape['status'] = $project->status;
            $shape['images'] = $project->getMedia('project_images')
                ->map(fn ($media) => $media->getUrl())->values();
        }

        return $shape;
    }

    // ─────────────────────────────────────────────────────────────

    private function filterOptions(string $model): \Illuminate\Support\Collection
    {
        return $model::orderBy('name')->get(['name', 'slug'])
            ->map(fn ($m) => ['name' => $m->name, 'slug' => $m->slug]);
    }

    /** Shared by v1 and the legacy endpoint so filter behaviour cannot drift. */
    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->string('search') . '%');
        }

        // Taxonomy filters, all by slug.
        $this->applySlugFilter($query, $request, 'category', 'category');
        $this->applySlugFilter($query, $request, 'sector', 'sectors');
        $this->applySlugFilter($query, $request, 'installation_type', 'installationTypes');
        $this->applySlugFilter($query, $request, 'product_variant', 'productVariants');

        if ($request->filled('location')) {
            $query->whereHas('location', fn ($q) => $q->where('slug', $request->string('location')));
        }
    }

    private function buildIndex(Request $request): array
    {
        $query = PortfolioProject::query()
            ->where('status', 'published')
            ->with(self::RELATIONS);

        $this->applyFilters($query, $request);
        $query->orderByDesc('created_at');

        $perPage  = min($request->integer('per_page', 12), self::MAX_PER_PAGE);
        $projects = $query->paginate($perPage);

        return [
            'data' => ProjectSummaryResource::collection($projects->getCollection()),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page'    => $projects->lastPage(),
                'per_page'     => $projects->perPage(),
                // Drives "Total projects (120)" on the listing screen.
                'total'        => $projects->total(),
            ],
        ];
    }

    private function applySlugFilter(Builder $query, Request $request, string $param, string $relation): void
    {
        if ($request->filled($param)) {
            $query->whereHas($relation, fn ($q) => $q->where('slug', $request->string($param)));
        }
    }

    private function buildShow(string $slug): array
    {
        $project = PortfolioProject::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with([...self::RELATIONS, 'productVariants.product:id,name,slug'])
            ->firstOrFail();

        // Prefer projects sharing this one's category, then fall back to the
        // most recent, so the rail is never empty on a thin catalogue.
        $related = PortfolioProject::query()
            ->where('status', 'published')
            ->whereKeyNot($project->id)
            ->with(self::RELATIONS)
            ->orderByRaw('category_id = ? DESC', [$project->category_id])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'data' => (new ProjectDetailResource($project))->withRelated($related),
        ];
    }
}
