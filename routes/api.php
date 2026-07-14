<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\PortfolioProjectController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;

Route::get('/filters', [FilterController::class, 'index']);

Route::get('/portfolio', [PortfolioProjectController::class, 'index']);
Route::get('/portfolio/{slug}', [PortfolioProjectController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);



Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{slug}', [ServiceController::class, 'show']);