<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;



class ProductCategoryController extends Controller
{
 public function productsCategory(): JsonResponse
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
            'description' => $product->description,
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