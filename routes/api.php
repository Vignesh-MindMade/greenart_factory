<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\PortfolioProjectController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\BlogController;


/*
|--------------------------------------------------------------------------
| v1 — page and entity endpoints
|--------------------------------------------------------------------------
| Envelope: { data } or { data, meta }. Page endpoints serve one screen in one
| request; entity endpoints serve search, filtering and pagination.
| Full contract: docx/API-REFERENCE.md
*/
Route::prefix('v1')->group(function () {
    // Page endpoints — one request per screen.
    Route::get('/pages/products', [ProductController::class, 'page']);
    // One gallery page per collection; the same screen serves all of them.
    Route::get('/pages/gallery/{slug}', [GalleryController::class, 'show']);
    Route::get('/pages/portfolio', [PortfolioProjectController::class, 'page']);

    Route::get('/projects', [PortfolioProjectController::class, 'index']);
    Route::get('/projects/{slug}', [PortfolioProjectController::class, 'show']);

    // Entity endpoints.
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
});


/*
|--------------------------------------------------------------------------
| Legacy — unversioned, deprecated
|--------------------------------------------------------------------------
| Kept so nothing already pointed at these URLs breaks. Products routes now
| return the v1 payload plus the legacy success/message keys — additive only.
| Remove once the frontend has migrated to /api/v1.
*/

Route::get('/filters', [FilterController::class, 'index']);

Route::get('/portfolio', [PortfolioProjectController::class, 'legacyIndex']);
Route::get('/portfolio/{slug}', [PortfolioProjectController::class, 'legacyShow']);

Route::get('/products', [ProductController::class, 'legacyIndex']);
Route::get('/products/{slug}', [ProductController::class, 'legacyShow']);



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