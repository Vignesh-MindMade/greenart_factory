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

        // First published linked project drives the "View project" CTA.
        $project = $this->whenLoaded('portfolioProjects')
            ? $this->portfolioProjects->first()
            : null;

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

            'project_url' => $project ? '/portfolio/' . $project->slug : null,
            'gallery_url' => '/gallery?product=' . $this->product->slug . '&variant=' . $this->slug,
        ];
    }
}
