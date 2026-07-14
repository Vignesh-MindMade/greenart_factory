<?php

namespace App\Support\Media;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CloudinaryUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if ($this->media->disk !== 'cloudinary') {
            return parent::getUrl();
        }

        // Cache per UUID — one API call per media item per day
        // UUID never changes even if the file is replaced
        return Cache::remember(
            'cloudinary_url_' . $this->media->uuid,
            now()->addDay(),
            fn () => $this->fetchRealCloudinaryUrl()
        );
    }

    private function fetchRealCloudinaryUrl(): string
    {
        try {
            // getPathRelativeToRoot() = "31/service_living_plant_care.jpg"
            $path = $this->getPathRelativeToRoot();

            // $adapter->cloudinary is the Cloudinary SDK client
            // (confirmed accessible from earlier tinker session)
            $adapter = Storage::disk('cloudinary')->getAdapter();
            $cloudinary = $adapter->cloudinary;

            // Build the full public_id — same logic as CloudinaryPathNormalizer::prefixed()
            $folder = config('flysystem-cloudinary.folder'); // reads CLOUDINARY_FOLDER from .env
            $publicId = $folder
                ? trim($folder, '/') . '/' . ltrim($path, '/')
                : ltrim($path, '/');
            // Result: "gaf/31/service_living_plant_care.jpg"

            // Admin API — READ ONLY, returns asset metadata including real secure_url
            // This is the same API that returned the working URL in your earlier tinker test
            $asset = $cloudinary->adminApi()->asset($publicId);

            return $asset['secure_url'];
            // Returns: "https://res.cloudinary.com/.../v1784013495/gaf/31/service_living_plant_care.jpg.jpg"

        } catch (\Exception) {
            // Network error, asset not found, etc — fall back rather than crash
            return parent::getUrl();
        }
    }

    // Cloudinary has no concept of expiring signed URLs
    // Return the permanent public URL instead of throwing
    public function getTemporaryUrl(\DateTimeInterface $expiration, array $options = []): string
    {
        return $this->getUrl();
    }
}