<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageCompressionService
{
    /**
     * Compress image using GD and save as WebP under storage/app/public.
     */
    public function compressAndSave(UploadedFile $file, string $folder, int $maxWidth = 1600, int $quality = 78): string
    {
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return $file->store($folder, 'public');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        $sourceImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
            'image/gif' => @imagecreatefromgif($file->getRealPath()),
            default => null,
        };

        if (!$sourceImage) {
            return $file->store($folder, 'public');
        }

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $newWidth);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $filename = Str::random(40) . '.webp';
        $relativeStoragePath = trim($folder, '/') . '/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativeStoragePath);

        $dir = dirname($absolutePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagewebp($targetImage, $absolutePath, $quality);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $relativeStoragePath;
    }
}
