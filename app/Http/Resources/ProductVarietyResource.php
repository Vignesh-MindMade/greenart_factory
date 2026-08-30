<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One tile in the "Choose Your Texture & Feel" grid on the product detail page.
 */
class ProductVarietyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'label'       => $this->label,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'image'       => $this->getFirstMediaUrl('variety_image') ?: null,
            // The gallery is per collection — there is no variety-scoped view.
            'gallery_url' => '/gallery/' . $this->product->slug,
        ];
    }
}
