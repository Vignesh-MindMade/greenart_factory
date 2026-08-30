<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The full product detail page (Figma node 545:571).
 *
 * Composes the shared variant/variety/summary resources rather than
 * re-serialising those shapes, so a field is defined in exactly one place.
 */
class ProductDetailResource extends JsonResource
{
    /** @var \Illuminate\Support\Collection */
    protected $related;

    public function withRelated($related): static
    {
        $this->related = $related;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->getFirstMediaUrl('cover_image') ?: null,

            'variants' => ProductVariantResource::collection($this->variants),

            'varieties_section' => [
                'title'  => $this->varieties_title,
                'intro'  => $this->varieties_intro,
                'footer' => $this->varieties_footer,
                'items'  => ProductVarietyResource::collection($this->varieties),
            ],

            'related' => ProductSummaryResource::collection($this->related ?? collect()),
        ];
    }
}
