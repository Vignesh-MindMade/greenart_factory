<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One item in an ordered list — "Why Choose Us", benefits, process steps, FAQs.
 *
 * `value` and `icon` are null for most groups; they are here so the shape stays
 * identical across every list and the frontend needs one schema, not nine.
 */
class ContentBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'display_no'  => $this->display_no,
            'title'       => $this->title,
            'description' => $this->description,
            'value'       => $this->value,
            'icon'        => $this->getFirstMediaUrl('icon') ?: null,
        ];
    }
}
