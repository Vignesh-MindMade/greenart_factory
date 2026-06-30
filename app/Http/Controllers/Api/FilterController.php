<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InstallationType;
use App\Models\Location;
use App\Models\Product;
use App\Models\Sector;

class FilterController extends Controller
{
    public function index()
    {
        $sectors = Sector::orderBy('name')
            ->get(['id', 'name', 'slug']);

        $locations = Location::orderBy('name')
            ->get(['id', 'name', 'slug', 'country']);

        $installationTypes = InstallationType::orderBy('name')
            ->get(['id', 'name', 'slug']);

        $products = Product::with(['variants' => function ($query) {
                $query->select('id', 'product_id', 'name', 'slug')
                      ->orderBy('name');
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'sectors'            => $sectors,
            'locations'          => $locations,
            'installation_types' => $installationTypes,
            'products'           => $products,
        ]);
    }
}