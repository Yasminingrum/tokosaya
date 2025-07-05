// File: app/Helpers/ImageHelper.php

<?php

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

if (!function_exists('upload_image')) {
    /**
     * Upload and process image
     */
    function upload_image($file, $path = 'uploads', $sizes = null)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;

        // Store original image
        $storedPath = $file->storeAs($path, $filename, 'public');

        // Create different sizes if specified
        if ($sizes && config('tokosaya.media.image_sizes')) {
            $imageSizes = config('tokosaya.media.image_sizes');

            foreach ($sizes as $sizeName) {
                if (isset($imageSizes[$sizeName])) {
                    $size = $imageSizes[$sizeName];
                    create_image_size($storedPath, $sizeName, $size['width'], $size['height']);
                }
            }
        }

        return $storedPath;
    }
}

if (!function_exists('create_image_size')) {
    /**
     * Create resized version of image
     */
    function create_image_size($imagePath, $sizeName, $width, $height)
    {
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            return false;
        }

        $pathInfo = pathinfo($imagePath);
        $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $sizeName . '.' . $pathInfo['extension'];
        $newFullPath = storage_path('app/public/' . $newPath);

        $image = Image::make($fullPath);
        $image->fit($width, $height, function ($constraint) {
            $constraint->upsize();
        });
        $image->save($newFullPath, 90);

        return $newPath;
    }
}

if (!function_exists('get_image_url')) {
    /**
     * Get image URL with optional size
     */
    function get_image_url($imagePath, $size = null)
    {
        if (!$imagePath) {
            return asset('images/placeholder.jpg');
        }

        if ($size) {
            $pathInfo = pathinfo($imagePath);
            $sizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $size . '.' . $pathInfo['extension'];

            if (Storage::disk('public')->exists($sizedPath)) {
                $imagePath = $sizedPath;
            }
        }

        $baseUrl = config('tokosaya.integrations.enable_cdn') && config('tokosaya.integrations.cdn_url')
            ? config('tokosaya.integrations.cdn_url')
            : asset('storage');

        return $baseUrl . '/' . $imagePath;
    }
}

if (!function_exists('delete_image')) {
    /**
     * Delete image and all its sizes
     */
    function delete_image($imagePath)
    {
        if (!$imagePath) {
            return false;
        }

        // Delete original
        Storage::disk('public')->delete($imagePath);

        // Delete sized versions
        $pathInfo = pathinfo($imagePath);
        $imageSizes = config('tokosaya.media.image_sizes', []);

        foreach ($imageSizes as $sizeName => $size) {
            $sizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $sizeName . '.' . $pathInfo['extension'];
            Storage::disk('public')->delete($sizedPath);
        }

        return true;
    }
}
