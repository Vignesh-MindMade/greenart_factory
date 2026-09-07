<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One tile in a gallery. Wraps the plain array built by GalleryService rather
 * than a model, since images arrive from several sources — the sub-product
 * itself, linked projects, and the collection's own uploads.
 */
class GalleryImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'url' => $this['url'],
            'alt' => $this['alt'],
            // 'variant' / 'variety' — the sub-product's own upload
            // 'project' — pulled in from a linked portfolio project
            // 'product'  — uploaded directly against the collection
            'source'  => $this['source'],
            'project' => $this['project'],
        ];
    }
}
