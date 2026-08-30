<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A variant as a small card under its product — used by the products grid and
 * the homepage strip. The full block (description, spec card, project link)
 * is ProductVariantResource, used only on the detail page.
 */
class ProductVariantSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'slug'    => $this->slug,
            'image'   => $this->getFirstMediaUrl('variant_images') ?: null,

            // Null until the variant link target is decided. There is no
            // variant-level screen in the design, so the old
            // /products/{product}/{variant} value pointed at a route that
            // does not exist. Frontend hides the link while this is null.
            'cta_url' => null,
        ];
    }
}
