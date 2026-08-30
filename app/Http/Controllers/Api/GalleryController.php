<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryImageResource;
use App\Models\PageSection;
use App\Models\Product;
use App\Services\GalleryService;
use Illuminate\Http\JsonResponse;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $gallery)
    {
    }

    /**
     * GET /api/v1/pages/gallery/{slug}
     *
     * One collection's gallery. The same screen serves every collection —
     * hero, image grid, and a rail of the other collections.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with('media')
            ->firstOrFail();

        $section = PageSection::where('section_key', 'gallery')->first();

        // "Related Images" — the other collections, each linking to its own
        // gallery page.
        $related = Product::query()
            ->where('status', 'published')
            ->whereKeyNot($product->id)
            ->with('media')
            ->orderBy('name')
            ->limit(5)
            ->get();

        $images = $this->gallery->for($product);

        return response()->json([
            'data' => [
                'section' => [
                    'title'    => $section?->title,
                    'subtitle' => $section?->subtitle,
                ],
                'collection' => [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'slug'        => $product->slug,
                    'description' => $product->description,
                    // Falls back to the product cover so the page is never
                    // headerless before a dedicated banner is uploaded.
                    'hero_image'  => $product->getFirstMediaUrl('gallery_hero')
                        ?: ($product->getFirstMediaUrl('cover_image') ?: null),
                    'product_url' => '/products/' . $product->slug,
                ],
                'images' => GalleryImageResource::collection($images),

                // Rail links to sibling galleries, not to product pages, so
                // this carries gallery_url rather than ProductSummaryResource.
                'related' => $related->map(fn (Product $item) => [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'slug'        => $item->slug,
                    'cover_image' => $item->getFirstMediaUrl('cover_image') ?: null,
                    'gallery_url' => '/gallery/' . $item->slug,
                ])->values(),
            ],
            'meta' => [
                'total_images' => $images->count(),
            ],
        ]);
    }
}
