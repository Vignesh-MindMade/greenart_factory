<?php

namespace App\Http\Resources;

use App\Models\ContentBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The project detail screen (Figma node 640:2214).
 */
class ProjectDetailResource extends JsonResource
{
    /** @var \Illuminate\Support\Collection */
    protected $related;

    public function withRelated($related): static
    {
        $this->related = $related;

        return $this;
    }

    /**
     * Some specification rows are already modelled on the project, so their
     * value is derived rather than typed — otherwise the two copies drift the
     * moment someone edits one and not the other.
     *
     * Returns null when the label is not a reserved one, in which case the
     * editor's typed value is used.
     */
    private function autoValue(?string $label): ?string
    {
        return match (mb_strtolower(trim((string) $label))) {
            'location' => $this->location?->name,
            default    => null,
        };
    }

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'excerpt'     => $this->excerpt,
            'status'      => $this->status,
            'hero_image'  => $this->getFirstMediaUrl('cover_image') ?: null,

            'category' => $this->category ? [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'location' => $this->location ? [
                'name'    => $this->location->name,
                'slug'    => $this->location->slug,
                'country' => $this->location->country,
            ] : null,
            'sectors' => $this->sectors->map(fn ($s) => ['name' => $s->name, 'slug' => $s->slug])->values(),
            'installation_types' => $this->installationTypes
                ->map(fn ($i) => ['name' => $i->name, 'slug' => $i->slug])->values(),

            // Home > Interior Projects > Artificial tree installation
            'breadcrumb' => array_values(array_filter([
                ['label' => 'Home', 'url' => '/'],
                $this->category ? [
                    'label' => $this->category->name,
                    'url'   => '/portfolio?category=' . $this->category->slug,
                ] : null,
                ['label' => $this->title, 'url' => null],
            ])),

            'content' => [
                'execution'      => $this->execution,
                'key_stages'     => $this->key_stages,
                'key_highlights' => $this->key_highlights,
                // Stored separately per BRD FR-2.3; the design renders them as
                // one "our challenge & solution" block.
                'challenge'      => $this->challenge,
                'solution'       => $this->solution,
            ],

            // The dark card beside the challenge & solution block.
            'spec_card' => [
                'eyebrow'  => $this->spec_eyebrow,
                'headline' => $this->spec_headline,
                'body'     => $this->spec_body,
                'rows'     => $this->whenLoaded(
                    'contentBlocks',
                    fn () => $this->contentBlocks
                        ->where('group', ContentBlock::GROUP_PROJECT_SPEC)
                        ->where('status', 'published')
                        ->map(fn ($b) => [
                            'label' => $b->title,
                            'value' => $this->autoValue($b->title) ?? $b->value,
                        ])
                        ->values(),
                    []
                ),
            ],

            'gallery' => $this->getMedia('project_images')
                ->map(fn ($media) => [
                    'url' => $media->getUrl(),
                    'alt' => $media->name ?: $this->title,
                ])->values(),

            // Collections this project is tagged to, via product_variants.
            'collections' => $this->productVariants
                ->map(fn ($v) => $v->product)
                ->filter()
                ->unique('id')
                ->map(fn ($p) => [
                    'name'        => $p->name,
                    'slug'        => $p->slug,
                    'product_url' => '/products/' . $p->slug,
                    'gallery_url' => '/gallery/' . $p->slug,
                ])->values(),

            'related' => ProjectSummaryResource::collection($this->related ?? collect()),
        ];
    }
}
