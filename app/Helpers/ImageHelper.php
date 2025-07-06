<?php

use Illuminate\Support\Str;
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
     * Create resized version of image using GD library
     *
     * @param string $imagePath
     * @param string $sizeName
     * @param int $width
     * @param int $height
     * @return bool
     */
    function create_image_size($imagePath, $sizeName, $width, $height)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            Logger()->warning('GD extension not available for image resizing');
            return false;
        }

        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            return false;
        }

        try {
            $pathInfo = pathinfo($imagePath);
            $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $sizeName . '.' . $pathInfo['extension'];
            $newFullPath = storage_path('app/public/' . $newPath);

            // Get image info
            $imageInfo = getimagesize($fullPath);
            if (!$imageInfo) {
                return false;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $imageType = $imageInfo[2];

            // Create image resource from file
            $sourceImage = null;
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($fullPath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($fullPath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($fullPath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = imagecreatefromwebp($fullPath);
                    }
                    break;
                default:
                    return false;
            }

            if (!$sourceImage) {
                return false;
            }

            // Calculate new dimensions maintaining aspect ratio
            $aspectRatio = $originalWidth / $originalHeight;
            $targetAspectRatio = $width / $height;

            if ($aspectRatio > $targetAspectRatio) {
                // Image is wider than target
                $newWidth = $width;
                $newHeight = intval($width / $aspectRatio);
            } else {
                // Image is taller than target
                $newWidth = intval($height * $aspectRatio);
                $newHeight = $height;
            }

            // Create new image canvas
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize the image
            imagecopyresampled(
                $resizedImage, $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );

            // Save the resized image
            $result = false;
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $result = imagejpeg($resizedImage, $newFullPath, 90);
                    break;
                case IMAGETYPE_PNG:
                    $result = imagepng($resizedImage, $newFullPath, 8);
                    break;
                case IMAGETYPE_GIF:
                    $result = imagegif($resizedImage, $newFullPath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagewebp')) {
                        $result = imagewebp($resizedImage, $newFullPath, 90);
                    }
                    break;
            }

            // Clean up memory
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            return $result;

        } catch (\Exception $e) {
            Logger()->error('Image resize failed: ' . $e->getMessage());
            return false;
        }
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
