<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A project card on the portfolio grid, the homepage featured strip, and the
 * related-projects rail.
 *
 * `meta_line` is pre-joined server-side: the design shows "Commercial · Dubai ·
 * UAE" with bullet separators, and which parts exist varies per project.
 * Building it here keeps that conditional logic out of the frontend.
 */
class ProjectSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $parts = array_filter([
            $this->whenLoaded('sectors') ? $this->sectors->first()?->name : null,
            $this->location?->name,
            $this->location?->country,
        ]);

        $cover = $this->getFirstMediaUrl('cover_image') ?: null;

        // Cover first, then the gallery, deduplicated by URL — always at
        // least [cover] when only a cover is set, so the frontend never has
        // to special-case an empty array against a separate cover_image
        // field when rotating cards.
        $gallery = collect($cover ? [['url' => $cover, 'alt' => $this->title]] : [])
            ->merge($this->getMedia('project_images')->map(fn ($media) => [
                'url' => $media->getUrl(),
                'alt' => $media->name ?: $this->title,
            ]))
            ->unique('url')
            ->values();

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'excerpt'     => $this->excerpt,
            'cover_image' => $cover,
            'gallery'     => $gallery,

            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]),
            'location' => $this->location ? [
                'name'    => $this->location->name,
                'slug'    => $this->location->slug,
                'country' => $this->location->country,
            ] : null,
            'sectors' => $this->whenLoaded(
                'sectors',
                fn () => $this->sectors->map(fn ($s) => ['name' => $s->name, 'slug' => $s->slug])->values()
            ),

            // Ready to render as-is under the card title.
            'meta_line' => $parts ? implode(' · ', $parts) : null,
            'cta_url'   => '/portfolio/' . $this->slug,
        ];
    }
}
