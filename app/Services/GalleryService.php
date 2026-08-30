<?php

namespace App\Services;

use App\Models\PortfolioProject;
use App\Models\Product;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Builds a collection's gallery — BRD section 6.
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
     * Every image that should appear on this collection's gallery page.
     *
     * @return Collection<int, array{url: string, alt: string, source: string, project: ?array}>
     */
    public function for(Product $product): Collection
    {
        return $this->fromProjects($product)
            ->concat($this->fromProduct($product))
            ->values();
    }

    /**
     * Cover images and flagged gallery images from every published project
     * linked to this collection through the product_variants pivot.
     */
    private function fromProjects(Product $product): Collection
    {
        $projects = PortfolioProject::query()
            ->where('status', 'published')
            ->whereHas(
                'productVariants',
                fn ($q) => $q->where('product_variants.product_id', $product->id)
            )
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
