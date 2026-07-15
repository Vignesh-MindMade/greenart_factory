<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->where('status', 'published')
            ->with(['variants' => fn ($q) => $q->orderBy('name')]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search') . '%');
        }

        $products = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $products->map(fn ($product) => [
                'id'       => $product->id,
                'name'     => $product->name,
                'slug'     => $product->slug,
                'cover_image' => $product->getFirstMediaUrl('products'),
                'variants' => $product->variants->map(fn ($variant) => [
                    'id'     => $variant->id,
                    'name'   => $variant->name,
                    'slug'   => $variant->slug,
                    'images' => $variant->getMedia('variant_images')
                        ->map(fn ($media) => $media->getUrl())
                        ->values(),
                ]),
            ]),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['variants' => fn ($q) => $q->orderBy('name')])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'id'       => $product->id,
                'name'     => $product->name,
                'slug'     => $product->slug,
                'cover_image' => $product->getFirstMediaUrl('products'),
                'variants' => $product->variants->map(fn ($variant) => [
                    'id'     => $variant->id,
                    'name'   => $variant->name,
                    'slug'   => $variant->slug,
                    'images' => $variant->getMedia('variant_images')
                        ->map(fn ($media) => $media->getUrl())
                        ->values(),
                ]),
            ],
        ]);
    }
}