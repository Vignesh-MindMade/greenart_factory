<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductSummaryResource;
use App\Models\ContentBlock;
use App\Models\PageSection;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Hard ceiling so ?per_page=1000000 cannot be used to dump the catalogue. */
    private const MAX_PER_PAGE = 48;

    // ─────────────────────────────────────────────────────────────
    //  v1 — page and entity endpoints, { data, meta } envelope
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/pages/products — everything the products listing page renders.
     */
    public function page(): JsonResponse
    {
        $section = PageSection::query()
            ->where('section_key', 'products')
            ->with([
                'contentBlocks' => fn ($q) => $q
                    ->published()
                    ->group(ContentBlock::GROUP_WHY_CHOOSE_US),
                'contentBlocks.media',
            ])
            ->first();

        $products = Product::query()
            ->where('status', 'published')
            ->with([
                'media',
                'variants' => fn ($q) => $q->where('status', 'published'),
                'variants.media',
                'variants.product',
            ])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => [
                'section' => [
                    'title'       => $section?->title,
                    'subtitle'    => $section?->subtitle,
                    'description' => $section?->description,
                    'cta_label'   => $section?->cta_label,
                    'cta_url'     => $section?->cta_url,
                ],
                'products' => ProductSummaryResource::collection($products),

                'why_choose_us' => ContentBlockResource::collection(
                    $section?->contentBlocks ?? collect()
                ),
            ],
        ]);
    }

    /**
     * GET /api/v1/products — entity list for search, filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->buildIndex($request));
    }

    /**
     * GET /api/v1/products/{slug} — the product detail page.
     */
    public function show(string $slug): JsonResponse
    {
        return response()->json($this->buildShow($slug));
    }

    // ─────────────────────────────────────────────────────────────
    //  Legacy — unprefixed routes, { success, message, data, meta }
    //  Same payload as v1 plus the two legacy keys, so this is additive
    //  and safe for anything already consuming these URLs.
    //  Delete once the frontend has moved to /api/v1.
    // ─────────────────────────────────────────────────────────────

    public function legacyIndex(Request $request): JsonResponse
    {
        return response()->json(
            ['success' => true, 'message' => 'Success'] + $this->buildIndex($request)
        );
    }

    public function legacyShow(string $slug): JsonResponse
    {
        return response()->json(
            ['success' => true, 'message' => 'Success'] + $this->buildShow($slug)
        );
    }

    // ─────────────────────────────────────────────────────────────

    private function buildIndex(Request $request): array
    {
        $perPage = min($request->integer('per_page', 15), self::MAX_PER_PAGE);

        $query = Product::query()
            ->where('status', 'published')
            ->with([
                'media',
                'variants' => fn ($q) => $q->where('status', 'published'),
                'variants.media',
                'variants.product',
            ]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search') . '%');
        }

        $products = $query->orderBy('name')->paginate($perPage);

        return [
            'data' => ProductSummaryResource::collection($products->getCollection()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ];
    }

    private function buildShow(string $slug): array
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'media',
                'variants' => fn ($q) => $q->where('status', 'published'),
                'variants.media',
                'variants.product',
                // Drives the "View project" CTA on each block.
                'variants.portfolioProjects' => fn ($q) => $q
                    ->where('portfolio_projects.status', 'published')
                    ->select('portfolio_projects.id', 'title', 'portfolio_projects.slug'),
                'varieties' => fn ($q) => $q->where('status', 'published'),
                'varieties.media',
                'varieties.product',
            ])
            ->firstOrFail();

        $related = Product::query()
            ->where('status', 'published')
            ->whereKeyNot($product->id)
            ->with('media')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return [
            'data' => (new ProductDetailResource($product))->withRelated($related),
        ];
    }
}
