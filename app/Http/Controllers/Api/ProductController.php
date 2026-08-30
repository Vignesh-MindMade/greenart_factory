<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->where('status', 'published')
            ->with(['media', 'variants.media']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search') . '%');
        }

        $products = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $products->map(fn ($product) => [
                'id'       => $product->id,
                'name'     => $product->name,
                'slug'     => $product->slug,
                'description' => $product->description,
                'cover_image' => $product->getFirstMediaUrl('cover_image'),
                'variants' => $product->variants->map(fn ($variant) => [
                    'id'     => $variant->id,
                    'name'   => $variant->name,
                    'slug'   => $variant->slug,
                    'images' => $variant->getMedia('variant_images')
                        ->map(fn ($media) => $media->getUrl())
                        ->values(),
                ]),
            ]),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * Product detail page: the numbered variant blocks, the varieties texture
     * grid, and the related-products rail.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'media',
                'variants.media',
                // Drives the "View project" link on each block.
                'variants.portfolioProjects' => fn ($q) => $q
                    ->where('status', 'published')
                    ->select('portfolio_projects.id', 'title', 'portfolio_projects.slug'),
                'varieties.media',
            ])
            ->firstOrFail();

        $related = Product::query()
            ->where('status', 'published')
            ->whereKeyNot($product->id)
            ->with('media')
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'id'          => $item->id,
                'name'        => $item->name,
                'slug'        => $item->slug,
                'cover_image' => $item->getFirstMediaUrl('cover_image'),
                'cta_url'     => '/products/' . $item->slug,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'description' => $product->description,
                'cover_image' => $product->getFirstMediaUrl('cover_image'),

                'variants' => $product->variants->map(function ($variant) use ($product) {
                    $images = $variant->getMedia('variant_images')
                        ->map(fn ($media) => $media->getUrl())
                        ->values();

                    $project = $variant->portfolioProjects->first();

                    return [
                        'id'          => $variant->id,
                        'display_no'  => $variant->display_no,
                        'name'        => $variant->name,
                        'slug'        => $variant->slug,
                        'description' => $variant->description,
                        // First image is the block image; the rest feed the gallery.
                        'image'       => $images->first(),
                        'images'      => $images,
                        'spec'        => [
                            'label' => $variant->spec_label,
                            'value' => $variant->spec_value,
                            'tags'  => $variant->spec_tags ?? [],
                        ],
                        'project_url' => $project ? '/portfolio/' . $project->slug : null,
                        'gallery_url' => '/gallery?product=' . $product->slug . '&variant=' . $variant->slug,
                    ];
                }),

                'varieties_section' => [
                    'title'  => $product->varieties_title,
                    'intro'  => $product->varieties_intro,
                    'footer' => $product->varieties_footer,
                    'items'  => $product->varieties->map(fn ($variety) => [
                        'id'          => $variety->id,
                        'label'       => $variety->label,
                        'name'        => $variety->name,
                        'slug'        => $variety->slug,
                        'description' => $variety->description,
                        'image'       => $variety->getFirstMediaUrl('variety_image'),
                        'gallery_url' => '/gallery?product=' . $product->slug . '&variety=' . $variety->slug,
                    ]),
                ],

                'related' => $related,
            ],
        ]);
    }
}