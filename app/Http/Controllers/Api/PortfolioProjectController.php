<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortfolioProject;

class PortfolioProjectController extends Controller
{
    //
    public function index (Request $request)
    {
            $query = PortfolioProject::query()
            ->where('status', 'published')
            ->with(['category:id,name,slug',
                'location:id,name,slug,country',
                'sectors:id,name,slug',
                'installationTypes:id,name,slug',
                'productVariants:id,product_id,name,slug',
                'productVariants.product:id,name,slug',]);

           if ($request->filled('sector')){
               $query->whereHas('sectors', function ($q) use ($request) {
                   $q->where('slug', $request->sector);
               });
           }

           if ($request->filled('location')){
               $query->whereHas('location', function ($q) use ($request) {
                   $q->where('slug', $request->location);
               });
           }

           if ($request->filled('installation_type')){
               $query->whereHas('installationTypes', function ($q) use ($request) {
                   $q->where('slug', $request->installation_type);
               });
           }

           if ($request->filled('product_variant')){
               $query->whereHas('productVariants', function ($q) use ($request) {
                   $q->where('slug', $request->product_variant);
               });
           }

           $projects = $query->orderByDesc('created_at')->get();

       return response()->json($projects->map(fn ($project) => [
            'id'    => $project->id,
            'title' => $project->title,
            'slug'  => $project->slug,

            // Single cover image URL — Spatie method
            'cover_image' => $project->getFirstMediaUrl('cover_image'),

            // Eager-loaded relations — no extra queries
            'category' => $project->category?->name,
            'location' => $project->location ? [
                'name'    => $project->location->name,
                'country' => $project->location->country,
            ] : null,

            // Pluck returns a flat array of values
            'sectors'            => $project->sectors->pluck('name'),
            'installation_types' => $project->installationTypes->pluck('name'),

            // Variants need product context for Next.js filter matching
            'product_variants' => $project->productVariants->map(fn ($v) => [
                'id'      => $v->id,
                'name'    => $v->name,
                'slug'    => $v->slug,
                'product' => $v->product?->name,
            ]),
        ]));
    }


     public function show(string $slug)
    {
        $project = PortfolioProject::where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'category:id,name,slug',
                'location:id,name,slug,country',
                'sectors:id,name,slug',
                'installationTypes:id,name,slug',
                'productVariants:id,product_id,name,slug',
                'productVariants.product:id,name,slug',
            ])
            ->firstOrFail();
        // ↑ firstOrFail() → auto-returns 404 JSON if slug not found
        //   Next.js catches this and shows its own 404 page

        return response()->json([
            'id'          => $project->id,
            'title'       => $project->title,
            'slug'        => $project->slug,
            'status'      => $project->status,

            'category' => $project->category?->name,
            'location' => $project->location ? [
                'name'    => $project->location->name,
                'country' => $project->location->country,
            ] : null,

            'sectors'            => $project->sectors->pluck('name'),
            'installation_types' => $project->installationTypes->pluck('name'),
            'product_variants'   => $project->productVariants->map(fn ($v) => [
                'id'      => $v->id,
                'name'    => $v->name,
                'slug'    => $v->slug,
                'product' => $v->product?->name,
            ]),

            // Cover image — single URL
            'cover_image' => $project->getFirstMediaUrl('cover_image'),

            // Gallery — array of all image URLs
            'images' => $project->getMedia('project_images')
                ->map(fn ($media) => $media->getUrl()),
            // ↑ getMedia() returns collection, map() converts each to URL
            // Result: ["https://res.cloudinary.com/.../1.jpg", "https://...2.jpg"]
        ]);
    }

}
