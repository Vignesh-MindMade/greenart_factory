<?php

namespace App\Services;

use App\Models\PortfolioProject;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariety;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Builds a gallery — BRD section 6.
 *
 * The sync is *reference-based and read-time*: images are stored once and
 * gathered on request, never copied. That is what the BRD asks for, and it
 * makes the rest of the requirement fall out for free —
 *
 *   "if a project is unpublished, archived, or deleted, its images are
 *    automatically removed from the linked product gallery view"
 *
 * — because unpublished projects simply stop matching the query. No sync job,
 * no cache to invalidate, no orphaned rows.
 *
 * A gallery is either collection-wide (a Product) or scoped to one sub-product
 * — a variant or a variety — which each get their own gallery page.
 */
class GalleryService
{
    /**
     * Custom property on a project image. Absent or true = shown in galleries.
     * Set false to exclude an internal or before-work photo (BRD section 6,
     * final bullet).
     */
    public const SHOW_IN_GALLERY = 'show_in_gallery';

    /**
     * Every image that should appear on this gallery page.
     *
     * Ordering is deliberate: the sub-product's own images first (they are
     * what the visitor clicked through for), then project photography, then
     * the collection's standalone uploads — which are included at every scope
     * so a thinly-linked sub-product never renders an empty page.
     *
     * @param  ProductVariant|ProductVariety|null  $scope  null = the whole collection
     * @return Collection<int, array{url: string, alt: string, source: string, project: ?array}>
     */
    public function for(Product $product, ProductVariant|ProductVariety|null $scope = null): Collection
    {
        return $this->fromScope($scope)
            ->concat($this->fromProjects($product, $scope))
            ->concat($this->fromProduct($product))
            ->unique('url')
            ->values();
    }

    /** The sub-product's own uploaded images. Empty for a collection gallery. */
    private function fromScope(ProductVariant|ProductVariety|null $scope): Collection
    {
        if ($scope === null) {
            return collect();
        }

        $collection = $scope instanceof ProductVariant ? 'variant_images' : 'variety_image';
        $source     = $scope instanceof ProductVariant ? 'variant' : 'variety';

        return $scope->getMedia($collection)
            ->map(fn (Media $media) => [
                'url'     => $media->getUrl(),
                'alt'     => $media->name ?: $scope->name,
                'source'  => $source,
                'project' => null,
            ]);
    }

    /**
     * Cover images and flagged gallery images from every published project
     * linked to this collection through the product_variants pivot.
     *
     * Scoped to a single variant when the gallery is a variant page. Varieties
     * carry no project link at all — they are textures of the collection, not
     * separately installable — so a variety gallery has no project source.
     */
    private function fromProjects(Product $product, ProductVariant|ProductVariety|null $scope): Collection
    {
        if ($scope instanceof ProductVariety) {
            return collect();
        }

        $projects = PortfolioProject::query()
            ->where('status', 'published')
            ->whereHas('productVariants', fn ($q) => $scope instanceof ProductVariant
                ? $q->where('product_variants.id', $scope->id)
                : $q->where('product_variants.product_id', $product->id))
            ->with(['media', 'category:id,name,slug'])
            ->orderByDesc('created_at')
            ->get();

        return $projects->flatMap(function (PortfolioProject $project) {
            $context = [
                'id'    => $project->id,
                'title' => $project->title,
                'slug'  => $project->slug,
                'url'   => '/portfolio/' . $project->slug,
            ];

            return $project->getMedia('cover_image')
                ->concat($project->getMedia('project_images'))
                ->filter(fn (Media $media) => $this->isShown($media))
                ->map(fn (Media $media) => [
                    'url'     => $media->getUrl(),
                    'alt'     => $media->name ?: $project->title,
                    'source'  => 'project',
                    'project' => $context,
                ]);
        });
    }

    /** Images uploaded directly against the collection. */
    private function fromProduct(Product $product): Collection
    {
        return $product->getMedia('product_gallery')
            ->map(fn (Media $media) => [
                'url'     => $media->getUrl(),
                'alt'     => $media->name ?: $product->name,
                'source'  => 'product',
                'project' => null,
            ]);
    }

    /** Default is to include — the admin opts an image out, not in. */
    private function isShown(Media $media): bool
    {
        return $media->getCustomProperty(self::SHOW_IN_GALLERY, true) !== false;
    }
}
