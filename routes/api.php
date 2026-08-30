<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\PortfolioProjectController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\BlogController;


Route::get('/filters', [FilterController::class, 'index']);

Route::get('/portfolio', [PortfolioProjectController::class, 'index']);
Route::get('/portfolio/{slug}', [PortfolioProjectController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);



Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{slug}', [ServiceController::class, 'show']);
// ── HOMEPAGE INDIVIDUAL SECTIONS (new) ────────────────────
Route::prefix('homepage')->group(function () {
    Route::get('/sections',         [HomepageController::class, 'sections']);
    Route::get('/hero',             [HomepageController::class, 'hero']);
    Route::get('/featured',         [HomepageController::class, 'featured']);
    Route::get('/testimonials',     [HomepageController::class, 'testimonials']);
    Route::get('/partners',         [HomepageController::class, 'partners']);
    Route::get('/blog-preview',     [HomepageController::class, 'blogPreview']);
    Route::get('/products-preview', [HomepageController::class, 'productsPreview']);
});


// Product Category
// Same payload as /api/homepage/products-preview — kept as a separate URL for the
// products page. Split the implementations if the two ever need to diverge.
Route::prefix('productcategory')->group(function(){
    Route::get('/categories',[HomepageController::class, 'productsPreview']);
});

// ── BLOG ──────────────────────────────────────────────────
Route::get('/blog',                 [BlogController::class, 'index']);
Route::get('/blog/{slug}',          [BlogController::class, 'show']);