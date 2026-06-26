<?php

namespace App\Providers;

use Cloudinary\Cloudinary;
use CodebarAg\FlysystemCloudinary\CloudinaryDiskOptions;
use CodebarAg\FlysystemCloudinary\FlysystemCloudinaryAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use League\Flysystem\Filesystem;
use League\Flysystem\WhitespacePathNormalizer;

class CloudinaryFilesystemServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('cloudinary', function (Application $app, array $config) {
            // Remove the 'driver' key as it's not needed by the Cloudinary SDK
            $configForCloudinary = $config;
            unset($configForCloudinary['driver']);

            $diskOptions = CloudinaryDiskOptions::fromDiskAndConfig($config);
            $adapter = new FlysystemCloudinaryAdapter(new Cloudinary($configForCloudinary), $diskOptions);

            return new FilesystemAdapter(
                new Filesystem($adapter, [], new WhitespacePathNormalizer()),
                $adapter,
                $config
            );
        });
    }
}