<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One numbered block on the product detail page: image, copy, specification
 * card, and the two CTAs.
 */
class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = $this->getMedia('variant_images')
            ->map(fn ($media) => $media->getUrl())
            ->values();

        // The "View project" CTA opens the portfolio listing pre-filtered to
        // this variant rather than jumping to one project, so every linked
        // project is reachable — including projects that carry several other
        // products alongside this one.
        $hasProjects = $this->relationLoaded('portfolioProjects')
            && $this->portfolioProjects->isNotEmpty();

        return [
            'id'          => $this->id,
            'display_no'  => $this->display_no,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,

            // First image is the block image; the full set feeds the gallery.
            'image'       => $images->first(),
            'images'      => $images,

            'spec' => [
                'label' => $this->spec_label,
                'value' => $this->spec_value,
                'tags'  => $this->spec_tags ?? [],
            ],

            // Carries the product too, so the listing can label the applied
            // filter with the collection name. Null when nothing is linked —
            // the CTA is hidden rather than opening an empty result set.
            'project_url' => $hasProjects
                ? '/portfolio?product=' . $this->product->slug . '&product_variant=' . $this->slug
                : null,
            'project_count' => $hasProjects ? $this->portfolioProjects->count() : 0,

            // Every sub-product has its own gallery page.
            'gallery_url' => '/gallery/' . $this->product->slug . '/' . $this->slug,
        ];
    }
}
