<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::query()
            ->where('status', 'published')
            ->with([
                'serviceitem' => fn ($q) => $q->orderBy('sort_order'),
            ]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search') . '%');
        }

        $services = $query
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $services->getCollection()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,

                    'items' => $service->serviceitem->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'slug' => $item->slug,
                            'sort_order' => $item->sort_order,

                            'cover_image' => $item->getFirstMediaUrl('cover_image'),

                            'project_images' => $item
                                ->getMedia('project_images')
                                ->map(fn ($media) => $media->getUrl())
                                ->values(),
                        ];
                    }),
                ];
            }),

            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'serviceitem' => fn ($q) => $q->orderBy('sort_order'),
            ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,

                'items' => $service->serviceitem->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'slug' => $item->slug,
                        'sort_order' => $item->sort_order,

                        'cover_image' => $item->getFirstMediaUrl('cover_image'),

                        'project_images' => $item
                            ->getMedia('project_images')
                            ->map(fn ($media) => $media->getUrl())
                            ->values(),
                    ];
                }),
            ],
        ]);
    }
}