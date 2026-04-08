<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Optimize an image from a path.
     *
     * @param string $path The absolute path to the image file.
     * @param int $maxWidth Max width to resize.
     * @param int $maxHeight Max height to resize.
     * @param int $quality Quality from 0 to 100.
     * @return bool
     */
    public static function optimize($path, $maxWidth = 1600, $maxHeight = 1600, $quality = 85)
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        try {
            $imageInfo = @getimagesize($path);
            if (!$imageInfo) return false;

            [$width, $height, $type] = $imageInfo;

            $image = null;

            // Load image using global namespace functions and check existence
            switch ($type) {
                case IMAGETYPE_JPEG:
                    if (!\function_exists('imagecreatefromjpeg')) return false;
                    $image = @\imagecreatefromjpeg($path);
                    break;
                case IMAGETYPE_PNG:
                    if (!\function_exists('imagecreatefrompng')) return false;
                    $image = @\imagecreatefrompng($path);
                    if ($image) {
                        \imagealphablending($image, false);
                        \imagesavealpha($image, true);
                    }
                    break;
                case IMAGETYPE_WEBP:
                    if (!\function_exists('imagecreatefromwebp')) return false;
                    $image = @\imagecreatefromwebp($path);
                    break;
            }

            if (!$image) return false;

            // Calculate new dimensions
            $ratio = $width / $height;
            $newWidth = $width;
            $newHeight = $height;

            if ($width > $maxWidth || $height > $maxHeight) {
                if ($maxWidth / $maxHeight > $ratio) {
                    $newWidth = (int)($maxHeight * $ratio);
                    $newHeight = $maxHeight;
                } else {
                    $newWidth = $maxWidth;
                    $newHeight = (int)($maxWidth / $ratio);
                }

                $newImage = \imagecreatetruecolor($newWidth, $newHeight);

                // Handle transparency for PNG/WebP
                if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
                    \imagealphablending($newImage, false);
                    \imagesavealpha($newImage, true);
                    $transparent = \imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    \imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                }

                \imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                \imagedestroy($image);
                $image = $newImage;
            }

            // Save back to same path
            switch ($type) {
                case IMAGETYPE_JPEG:
                    if (function_exists('imagejpeg')) {
                        \imagejpeg($image, $path, $quality);
                    }
                    break;
                case IMAGETYPE_PNG:
                    if (function_exists('imagepng')) {
                        // PNG compression is 0-9
                        $pngQuality = (int)((100 - $quality) / 10);
                        \imagepng($image, $path, $pngQuality);
                    }
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagewebp')) {
                        \imagewebp($image, $path, $quality);
                    }
                    break;
            }

            \imagedestroy($image);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Optimize an uploaded file before storing it.
     */
    public static function optimizeUpload($file, $maxWidth = 1600, $maxHeight = 1600, $quality = 85)
    {
        if (!$file || !method_exists($file, 'getRealPath')) return false;
        return self::optimize($file->getRealPath(), $maxWidth, $maxHeight, $quality);
    }
}
