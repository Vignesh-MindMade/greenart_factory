<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'department' => $this->department,
            'location' => $this->location,
            'employment_type' => $this->employment_type,
            'description' => $this->description,
            'posted_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
