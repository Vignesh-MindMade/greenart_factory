<?php

namespace App\Support\Media;

use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CloudinaryUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if ($this->media->disk !== 'cloudinary') {
            return parent::getUrl();
        }

        return Cache::remember(
            'cloudinary_url_' . $this->media->uuid,
            now()->addDay(),
            fn () => $this->buildOptimizedUrl()
        );
    }

    private function buildOptimizedUrl(): string
    {
        try {
            // "22/serenity-cover.png" — extension is part of public_id (codebar-ag behaviour)
            $path = $this->getPathRelativeToRoot();

            $folder = config('flysystem-cloudinary.folder');
            $publicId = $folder
                ? trim($folder, '/') . '/' . ltrim($path, '/')
                : ltrim($path, '/');
            // "gaf/22/serenity-cover.png"

            // Extract format from file_name — needed as URL suffix so Cloudinary
            // can match public_id correctly (which includes the extension)
            $extension = pathinfo($this->media->file_name, PATHINFO_EXTENSION) ?: 'jpg';
            // "png"

            $cloudName = config('filesystems.disks.cloudinary.cloud_name');

            // URL anatomy:
            //   /f_auto,q_auto/gaf/22/serenity-cover.png.png
            //   Cloudinary parses: public_id = "gaf/22/serenity-cover.png"  ← matches stored asset ✅
            //                      format suffix = ".png"                    ← overridden by f_auto ✅
            //   f_auto serves webp/avif/jpg based on browser Accept header
            //   q_auto picks optimal quality — typically 60-80% smaller than original
            return sprintf(
                'https://res.cloudinary.com/%s/image/upload/f_auto,q_auto/%s.%s',
                $cloudName,
                $publicId,
                $extension
            );
            // "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/22/serenity-cover.png.png"

        } catch (\Exception) {
            return parent::getUrl();
        }
    }

    public function getTemporaryUrl(\DateTimeInterface $expiration, array $options = []): string
    {
        return $this->getUrl();
    }
}