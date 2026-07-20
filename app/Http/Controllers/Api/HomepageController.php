<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPosts;
use App\Models\HeroSlides;
use App\Models\PageSection;
use App\Models\Partners;
use App\Models\PortfolioProject;
use App\Models\Product;
use App\Models\Testimonials;
use Illuminate\Http\JsonResponse;

class HomepageController extends Controller
{
    // GET /api/homepage/sections
    // Powers: all section titles, subtitles, CTAs across the page
    public function sections(): JsonResponse
    {
        return response()->json(
            PageSection::all()
                ->keyBy('section_key')
                ->map(fn ($s) => [
                    'title'       => $s->title,
                    'subtitle'    => $s->subtitle,
                    'description' => $s->description,
                    'cta_label'   => $s->cta_label,
                    'cta_url'     => $s->cta_url,
                ])
        );
    }

    // GET /api/homepage/hero
    // Powers: hero carousel slides
    public function hero(): JsonResponse
    {
        $slides = HeroSlides::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($slide) => [
                'id'        => $slide->id,
                'headline'  => $slide->headline,
                'subtext'   => $slide->subtext,
                'cta_label' => $slide->cta_label,
                'cta_url'   => $slide->cta_url,
                'image'     => $slide->getFirstMediaUrl('hero_slide_images'),
            ]);

        return response()->json(['data' => $slides]);
    }

    // GET /api/homepage/featured
    // Powers: Featured Projects carousel (admin-controlled via is_featured)
    public function featured(): JsonResponse
    {
        $projects = PortfolioProject::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->limit(6)
            ->with(['category:id,name,slug', 'location:id,name,slug','sectors:id,name,slug'])
            ->get()
            ->map(fn ($project) => [
                'id'          => $project->id,
                'title'       => $project->title,
                'slug'        => $project->slug,
                'cover_image' => $project->getFirstMediaUrl('cover_image'),
                'category'    => $project->category?->name,
                'sectors' => $project->sectors
                ->pluck('name')
                ->values(),
                'location'    => $project->location?->name,
            ]);

        return response()->json(['data' => $projects]);
    }

    // GET /api/homepage/testimonials
    // Powers: testimonials carousel
    public function testimonials(): JsonResponse
    {
        $testimonials = Testimonials::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($t) => [
                'id'             => $t->id,
                'customer_name'  => $t->customer_name,
                'customer_title' => $t->customer_title,
                'quote'          => $t->quote,
                'avatar'         => $t->getFirstMediaUrl('testimonial_image'),
            ]);

        return response()->json(['data' => $testimonials]);
    }

    // GET /api/homepage/partners
    // Powers: client logo strip
    public function partners(): JsonResponse
    {
        $partners = Partners::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'website_url' => $p->website_url,
                'logo'        => $p->getFirstMediaUrl('partner_logo'),
            ]);

        return response()->json(['data' => $partners]);
    }

    // GET /api/homepage/blog-preview
    // Powers: Blogs & Insights section (latest 6 posts + category filters)
    public function blogPreview(): JsonResponse
    {
        $posts = BlogPosts::query()
            ->where('status', 'published')
            ->where(fn ($q) => $q
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now())
            )
            ->latest('published_at')
            ->limit(6)
            ->with('blogs:id,name,slug')
            // ↑ 'category' = the method name on BlogPosts model — fix blogs() to category()
            ->get()
            ->map(fn ($post) => [
                'id'           => $post->id,
                'title'        => $post->title,
                'slug'         => $post->slug,
                'excerpt'      => $post->excerpt,
                'category'     => $post->blogs?->name,
                'published_at' => $post->published_at?->toISOString(),
                'image'        => $post->getFirstMediaUrl('cover_image'),
            ]);

        $categories = BlogCategory::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'data' => [
                'categories' => $categories,
                'posts'      => $posts,
            ],
        ]);
    }

// GET /api/homepage/products-preview
public function productsPreview(): JsonResponse
{
    $products = Product::query()
        ->where('status', 'published')
        ->with([
            'variants' => fn ($q) => $q->orderBy('name'),
        ])
        ->orderBy('name')
        ->get()
        ->map(fn ($product) => [
            'id'          => $product->id,
            'name'        => $product->name,
            'slug'        => $product->slug,
            // Product-level cover image (the large featured card)
            'cover_image' => $product->getFirstMediaUrl('cover_image'),
            // CTA: /products/moss-creations
            'cta_url'     => '/products/' . $product->slug,

            // Each variant = a smaller card in the grid
            'variants'    => $product->variants->map(fn ($v) => [
                'id'    => $v->id,
                'name'  => $v->name,
                'slug'  => $v->slug,
                // Variant-level image (each small card photo)
                'image' => $v->getFirstMediaUrl('variant_images'),
                // CTA: /products/moss-creations/moss-walls
                'cta_url' => '/products/' . $product->slug . '/' . $v->slug,
            ]),
        ]);

    return response()->json(['data' => $products]);
}
}