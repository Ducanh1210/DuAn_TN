<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Dịch vụ nén ảnh dùng thư viện GD: resize theo chiều rộng tối đa và xuất ra
 * định dạng WebP để giảm dung lượng, lưu vào storage/app/public.
 */
class ImageCompressionService
{
    /**
     * Nén ảnh và lưu dưới dạng WebP.
     *
     * @param UploadedFile $file Ảnh gốc tải lên
     * @param string $folder Thư mục con trong storage/app/public
     * @param int $maxWidth Chiều rộng tối đa (px); ảnh lớn hơn sẽ bị thu nhỏ
     * @param int $quality Chất lượng WebP 0-100
     * @return string Đường dẫn tương đối của ảnh đã lưu
     */
    public function compressAndSave(UploadedFile $file, string $folder, int $maxWidth = 1600, int $quality = 78): string
    {
        // Không đọc được thông tin ảnh -> lưu nguyên bản để không mất file
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return $file->store($folder, 'public');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Tạo ảnh nguồn theo đúng định dạng gốc
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

        // Tính kích thước mới, giữ nguyên tỉ lệ; chỉ thu nhỏ khi vượt maxWidth
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $newWidth);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        // Giữ nền trong suốt cho PNG/WebP
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
