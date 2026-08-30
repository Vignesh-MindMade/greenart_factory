<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A product as it appears in a grid or rail — the products listing page,
 * the homepage products strip, and the related-products rail on detail pages.
 *
 * Deliberately excludes variant blocks and varieties: those belong to
 * ProductDetailResource. Keeping them out is what stops the listing payload
 * growing every time the detail page gains a field.
 */
class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->getFirstMediaUrl('cover_image') ?: null,
            'cta_url'     => '/products/' . $this->slug,

            // Only present when the caller eager-loaded variants.
            'variants'    => ProductVariantSummaryResource::collection(
                $this->whenLoaded('variants')
            ),
        ];
    }
}
