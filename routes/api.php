<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FilterController;

Route::get('/filters', [FilterController::class, 'index']);