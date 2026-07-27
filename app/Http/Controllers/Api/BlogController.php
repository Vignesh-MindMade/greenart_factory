<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPosts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // GET /api/blog?category=green-walls&page=1
    public function index(Request $request): JsonResponse
    {
        $query = BlogPosts::query()
            ->where('status', 'published')
            ->where(fn ($q) => $q
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now())
            )
            ->with('blogs:id,name,slug');

        if ($request->filled('blogs')) {
            $query->whereHas('blogs', fn ($q) =>
                $q->where('slug', $request->blogs)
            );
        }

        $posts = $query
            ->latest('published_at')
            ->paginate($request->integer('per_page', 12));

        return response()->json([
            'data' => $posts->getCollection()->map(fn ($post) => [
                'id'           => $post->id,
                'title'        => $post->title,
                'slug'         => $post->slug,
                'excerpt' =>$post->excerpt,
                'content'      => $post->content,
                'blogs'     => [
                    'name' => $post->blogs?->name,
                    'slug' => $post->blogs?->slug,
                ],
                'author' => $post->author,
                'avatar'=>$post->getFirstMediaUrl('author_image'),
                'blog_date'=>$post->blog_date?->toISOString(),
                'published_at' => $post->published_at?->toISOString(),
                'image'        => $post->getFirstMediaUrl('cover_image'),
            ]),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
        ]);
    }

    // GET /api/blog/my-post-slug
    public function show(string $slug): JsonResponse
    {
        $post = BlogPosts::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where(fn ($q) => $q
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now())
            )
            ->with('blogs:id,name,slug')
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id'           => $post->id,
                'title'        => $post->title,
                'slug'         => $post->slug,
                'excerpt'      => $post->excerpt,
                'content'      => $post->content,
                'blogs'     => [
                    'name' => $post->blogs?->name,
                    'slug' => $post->blogs?->slug,
                ],
                'author' => $post->author,
                'avatar'=>$post->getFirstMediaUrl('author_image'),
                'blog_date'=>$post->blog_date?->toISOString(),
                'published_at' => $post->published_at?->toISOString(),
                'image'        => $post->getFirstMediaUrl('cover_image'),
            ],
        ]);
    }
}