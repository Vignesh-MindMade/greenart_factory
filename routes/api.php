<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\PortfolioProjectController;

Route::get('/filters', [FilterController::class, 'index']);

Route::get('/portfolio', [PortfolioProjectController::class, 'index']);