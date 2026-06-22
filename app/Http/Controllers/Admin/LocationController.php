<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\LocationImage;
use App\Models\Panorama;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $sortDir = $request->input('sort_dir', 'desc');
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $query = Location::with('category')->orderBy('id', $sortDir);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $locations = $query->paginate(20)->withQueryString();
        $categories = Category::where('status', 'active')->get();

        return view('admin.locations.index', compact('locations', 'categories', 'sortDir'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.locations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $path = $this->compressAndSaveImage($request->file('thumbnail'), 'locations/thumbnails');
            $data['thumbnail_url'] = $path;
        }

        $location = Location::create($data);

        return redirect()->route('admin.locations.edit', [$location->id] + $request->query())->with('success', 'Thêm địa điểm thành công! Vui lòng tiếp tục cập nhật hình ảnh và 360.');
    }

    public function edit(Location $location)
    {
        $categories = Category::where('status', 'active')->get();
        $images = $location->images;
        $panoramas = $location->panoramas;
        return view('admin.locations.edit', compact('location', 'categories', 'images', 'panoramas'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
        ]);

        $data = $request->all();
        if ($request->name !== $location->name) {
            $data['slug'] = Str::slug($request->name) . '-' . time();
        }
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh đại diện cũ nếu có
            if ($location->thumbnail_url) {
                Storage::disk('public')->delete($location->thumbnail_url);
            }
            $path = $this->compressAndSaveImage($request->file('thumbnail'), 'locations/thumbnails');
            $data['thumbnail_url'] = $path;
        }

        $location->update($data);

        return back()->with('success', 'Cập nhật địa điểm thành công!');
    }

    public function destroy(Location $location)
    {
        foreach($location->images as $img) {
            Storage::disk('public')->delete($img->image_url);
        }
        foreach($location->panoramas as $pano) {
            Storage::disk('public')->delete($pano->image_url);
        }
        if ($location->thumbnail_url) {
            Storage::disk('public')->delete($location->thumbnail_url);
        }
        if ($location->audio_url) {
            Storage::disk('public')->delete($location->audio_url);
        }
        
        $location->delete();
        return back()->with('success', 'Xóa địa điểm thành công!');
    }

    // Ajax Image Upload
    public function uploadImage(Request $request, Location $location)
    {
        $request->validate(['file' => 'required|image|max:20480']);
        $path = $this->compressAndSaveImage($request->file('file'), 'locations/images');

        $image = $location->images()->create([
            'image_url' => $path,
            'uploaded_by' => Auth::id(),
            'status' => 'approved'
        ]);

        return response()->json(['success' => true, 'image' => $image, 'url' => Storage::url($path)]);
    }

    public function deleteImage(LocationImage $image)
    {
        Storage::disk('public')->delete($image->image_url);
        $image->delete();
        return response()->json(['success' => true]);
    }

    // Ajax Panorama Upload
    public function uploadPanorama(Request $request, Location $location)
    {
        $request->validate(['file' => 'required|image|max:51200']);
        
        $file = $request->file('file');
        
        // Increase memory limit and max execution time for massive images (e.g. 18K panoramas)
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '300');
        
        $maxWidth = 11264; // 11K limit
        $info = getimagesize($file->getRealPath());
        
        if ($info && $info[0] > $maxWidth) {
            $path = $file->hashName('locations/panoramas');
            $absolutePath = storage_path('app/public/' . $path);
            
            // Ensure directory exists
            if (!file_exists(dirname($absolutePath))) {
                mkdir(dirname($absolutePath), 0755, true);
            }
            
            $width = $info[0];
            $height = $info[1];
            $mime = $info['mime'];
            
            $newWidth = $maxWidth;
            $newHeight = (int)(($height / $width) * $newWidth);
            
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            $sourceImage = null;
            
            if ($mime == 'image/jpeg') {
                $sourceImage = imagecreatefromjpeg($file->getRealPath());
                imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($newImage, $absolutePath, 90);
            } elseif ($mime == 'image/png') {
                $sourceImage = imagecreatefrompng($file->getRealPath());
                imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagepng($newImage, $absolutePath, 9);
            } else {
                $path = $file->store('locations/panoramas', 'public');
            }
            
            if ($sourceImage) {
                imagedestroy($sourceImage);
                imagedestroy($newImage);
            }
        } else {
            $path = $file->store('locations/panoramas', 'public');
        }

        $pano = $location->panoramas()->create([
            'scene_name' => 'Scene ' . time(),
            'image_url' => $path,
            'status' => 'active'
        ]);

        return response()->json(['success' => true, 'panorama' => $pano, 'url' => Storage::url($path)]);
    }

    public function deletePanorama(Panorama $panorama)
    {
        Storage::disk('public')->delete($panorama->image_url);
        $panorama->delete();
        return response()->json(['success' => true]);
    }

    // Ajax Audio Upload for Location
    public function uploadAudio(Request $request, Location $location)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:20480',
        ]);

        // Delete old audio if exists
        if ($location->audio_url && Storage::disk('public')->exists($location->audio_url)) {
            Storage::disk('public')->delete($location->audio_url);
        }

        $path = $request->file('audio')->store('locations/audio', 'public');
        
        $attributes = $location->attributes ?? [];
        if (isset($attributes['tts_text'])) {
            unset($attributes['tts_text']);
        }
        
        $location->update([
            'audio_url' => $path,
            'attributes' => $attributes
        ]);

        return response()->json([
            'success' => true,
            'audio_url' => asset('storage/' . $path),
        ]);
    }

    public function deleteAudio(Location $location)
    {
        if ($location->audio_url && Storage::disk('public')->exists($location->audio_url)) {
            Storage::disk('public')->delete($location->audio_url);
        }

        $attributes = $location->attributes ?? [];
        if (isset($attributes['tts_text'])) {
            unset($attributes['tts_text']);
        }

        $location->update([
            'audio_url' => null,
            'attributes' => $attributes
        ]);

        return response()->json(['success' => true]);
    }

    public function getTtsVoices()
    {
        try {
            $url = config('services.vieneu_tts.url') . '/voices';
            $response = Http::timeout(5)->get($url);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json([
                ['id' => 'error', 'name' => '⚠️ Máy chủ TTS báo lỗi hoặc chưa tải xong model.']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                ['id' => 'error', 'name' => '⚠️ Không thể kết nối tới máy chủ VieNeu-TTS (Cổng 8001).']
            ]);
        }
    }

    public function generateTtsAudio(Request $request, Location $location)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'voice_id' => 'nullable|string',
        ]);

        try {
            $url = config('services.vieneu_tts.url') . '/stream';
            
            // Gửi yêu cầu POST để tạo âm thanh cho các đoạn văn bản dài
            $response = Http::timeout(120)->post($url, [
                'text' => $request->text,
                'voice_id' => $request->voice_id,
                'emotion' => $request->input('emotion', 'natural'),
                'temperature' => (float) $request->input('temperature', 0.8),
                'top_k' => (int) $request->input('top_k', 25),
                'top_p' => (float) $request->input('top_p', 0.95),
                'repetition_penalty' => (float) $request->input('repetition_penalty', 1.2),
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi từ máy chủ TTS: ' . $response->status(),
                ], 500);
            }

            // Xóa file audio cũ nếu có
            if ($location->audio_url && Storage::disk('public')->exists($location->audio_url)) {
                Storage::disk('public')->delete($location->audio_url);
            }

            // Tạo tên file ngẫu nhiên đuôi .wav
            $filename = 'tts_' . $location->id . '_' . time() . '.wav';
            $path = 'locations/audio/' . $filename;

            // Lưu file âm thanh nhị phân
            Storage::disk('public')->put($path, $response->body());

            // Cập nhật đường dẫn và text tts vào DB
            $attributes = $location->attributes ?? [];
            $attributes['tts_text'] = $request->text;
            
            $location->update([
                'audio_url' => $path,
                'attributes' => $attributes
            ]);

            return response()->json([
                'success' => true,
                'audio_url' => asset('storage/' . $path),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối đến máy chủ VieNeu-TTS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Nén ảnh bằng thư viện GD và lưu dưới dạng WebP
     */
    private function compressAndSaveImage($file, $folder, $maxWidth = 1200, $quality = 75)
    {
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return $file->store($folder, 'public');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = @imagecreatefromjpeg($file->getRealPath());
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($file->getRealPath());
                break;
            case 'image/webp':
                $sourceImage = @imagecreatefromwebp($file->getRealPath());
                break;
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($file->getRealPath());
                break;
            default:
                $sourceImage = null;
        }

        if (!$sourceImage) {
            return $file->store($folder, 'public');
        }

        // Tính toán kích thước mới duy trì tỉ lệ khung hình
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)(($height / $width) * $newWidth);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        // Giữ độ trong suốt cho ảnh PNG và WebP
        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Đặt tên file ngẫu nhiên đuôi .webp
        $filename = Str::random(40) . '.webp';
        $relativeStoragePath = $folder . '/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativeStoragePath);

        // Tạo thư mục nếu chưa có
        $dir = dirname($absolutePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Xuất ảnh nén WebP
        imagewebp($targetImage, $absolutePath, $quality);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $relativeStoragePath;
    }
}
