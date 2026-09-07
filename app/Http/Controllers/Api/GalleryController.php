<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryImageResource;
use App\Models\PageSection;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariety;
use App\Services\GalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $gallery)
    {
    }

    /**
     * GET /api/v1/pages/gallery/{slug}
     * GET /api/v1/pages/gallery/{slug}/{child}
     *
     * A gallery page. The same screen serves every one of them — hero, image
     * grid, and a rail of sibling galleries.
     *
     * `{slug}` alone is the whole collection. Adding `{child}` scopes the page
     * to one sub-product — a variant or a variety — so every sub-product has
     * its own gallery URL rather than sharing the collection's.
     */
    public function show(string $slug, ?string $child = null): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with('media')
            ->firstOrFail();

        $scope = $child !== null ? $this->resolveChild($product, $child) : null;

        $section = PageSection::where('section_key', 'gallery')->first();
        $images  = $this->gallery->for($product, $scope);

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
                    'gallery_url' => '/gallery/' . $product->slug,
                ],

                // null on a collection gallery. On a sub-product gallery this
                // carries the title, copy and hero the page should render
                // instead of the collection's.
                'scope' => $scope ? $this->scopePayload($product, $scope) : null,

                'images' => GalleryImageResource::collection($images),

                // "Related Images" — sibling galleries. Other collections on a
                // collection page; the other sub-products of this collection on
                // a sub-product page, so the rail keeps the visitor inside it.
                'related' => $scope
                    ? $this->siblingGalleries($product, $scope)
                    : $this->otherCollections($product),
            ],
            'meta' => [
                'total_images' => $images->count(),
            ],
        ]);
    }

    /**
     * Variants first, then varieties — a slug is unique per product within
     * each table, and the two never collide in practice.
     */
    private function resolveChild(Product $product, string $child): ProductVariant|ProductVariety
    {
        $variant = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('slug', $child)
            ->where('status', 'published')
            ->with('media')
            ->first();

        if ($variant) {
            return $variant;
        }

        return ProductVariety::query()
            ->where('product_id', $product->id)
            ->where('slug', $child)
            ->where('status', 'published')
            ->with('media')
            ->firstOrFail();
    }

    private function scopePayload(Product $product, ProductVariant|ProductVariety $scope): array
    {
        $isVariant = $scope instanceof ProductVariant;

        return [
            'type'        => $isVariant ? 'variant' : 'variety',
            'id'          => $scope->id,
            'name'        => $scope->name,
            'slug'        => $scope->slug,
            'description' => $scope->description,
            'hero_image'  => $scope->getFirstMediaUrl($isVariant ? 'variant_images' : 'variety_image')
                ?: null,
            'product_url' => '/products/' . $product->slug,
            // The same filtered listing the product page's "View project" CTA
            // points at, so both routes land on one screen.
            'projects_url' => $isVariant
                ? '/portfolio?product=' . $product->slug . '&product_variant=' . $scope->slug
                : '/portfolio?product=' . $product->slug,
            // Home > Moss Creations > Moss Walls
            'breadcrumb' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => $product->name, 'url' => '/gallery/' . $product->slug],
                ['label' => $scope->name, 'url' => null],
            ],
        ];
    }

    /** The other sub-products of this collection, each with its own gallery. */
    private function siblingGalleries(Product $product, ProductVariant|ProductVariety $scope): Collection
    {
        $variants = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('status', 'published')
            ->with('media')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProductVariant $variant) => [
                'id'          => $variant->id,
                'type'        => 'variant',
                'name'        => $variant->name,
                'slug'        => $variant->slug,
                'cover_image' => $variant->getFirstMediaUrl('variant_images') ?: null,
                'gallery_url' => '/gallery/' . $product->slug . '/' . $variant->slug,
            ]);

        $varieties = ProductVariety::query()
            ->where('product_id', $product->id)
            ->where('status', 'published')
            ->with('media')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProductVariety $variety) => [
                'id'          => $variety->id,
                'type'        => 'variety',
                'name'        => $variety->name,
                'slug'        => $variety->slug,
                'cover_image' => $variety->getFirstMediaUrl('variety_image') ?: null,
                'gallery_url' => '/gallery/' . $product->slug . '/' . $variety->slug,
            ]);

        $currentType = $scope instanceof ProductVariant ? 'variant' : 'variety';

        return $variants->concat($varieties)
            ->reject(fn (array $item) => $item['type'] === $currentType && $item['id'] === $scope->id)
            ->take(5)
            ->values();
    }

    /** Sibling collections, each linking to its own gallery page. */
    private function otherCollections(Product $product): Collection
    {
        return Product::query()
            ->where('status', 'published')
            ->whereKeyNot($product->id)
            ->with('media')
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(fn (Product $item) => [
                'id'          => $item->id,
                'type'        => 'collection',
                'name'        => $item->name,
                'slug'        => $item->slug,
                'cover_image' => $item->getFirstMediaUrl('cover_image') ?: null,
                'gallery_url' => '/gallery/' . $item->slug,
            ])
            ->values();
    }
}
