<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use CodebarAg\FlysystemCloudinary\FlysystemCloudinaryAdapter;

$adapter = new FlysystemCloudinaryAdapter([
    'folder' => env('CLOUDINARY_FOLDER'),
    'uploadPreset' => null,
    'globalUploadOptions' => [],
    'preferSecureUrl' => true,
], [
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'url' => [
        'secure' => (bool) env('CLOUDINARY_SECURE_URL', true),
    ],
]);

$filesystem = new League\Flysystem\Filesystem(new League\Flysystem\WhitespacePathNormalizer(), $adapter);

try {
    $result = $filesystem->write('test.txt', 'hello');
    var_dump($result);
} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
}

?>