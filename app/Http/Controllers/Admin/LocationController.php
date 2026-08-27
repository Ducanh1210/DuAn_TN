<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\LocationImage;
use App\Models\Panorama;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Location\StoreLocationRequest;
use App\Http\Requests\Admin\Location\UpdateLocationRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

/**
 * Controller quản trị địa điểm (POI): CRUD địa điểm, upload/xóa ảnh, panorama 360°, audio,
 * tạo audio bằng TTS và xử lý xóa địa điểm doanh nghiệp (kèm thông báo + hạ vai trò chủ sở hữu).
 */
class LocationController extends Controller
{
    /** Danh sách địa điểm có tìm kiếm/lọc; tab thùng rác (xóa tạm); kèm chủ DN để bắt buộc lý do khi xóa. */
    public function index(Request $request)
    {
        $sortDir = $request->input('sort_dir', 'desc');
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $trash = $request->boolean('trash');
        $query = $trash
            ? Location::onlyTrashed()->with('category')->orderBy('deleted_at', $sortDir)
            : Location::with('category')->orderBy('id', $sortDir);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $locations = $query->paginate(20)->withQueryString();
        $categories = Category::where('status', 'active')->get();

        // Chủ DN đã duyệt (danh sách thường) + mọi hồ sơ DN (tab thùng rác: có thể đã bị hạ về rejected)
        $businessOwnerIds = BusinessProfile::where('status', 'approved')
            ->pluck('user_id')
            ->filter()
            ->values()
            ->all();

        if ($trash) {
            $trashedOwnerIds = $locations->pluck('created_by')->filter()->unique()->values()->all();
            $businessOwnerIds = array_values(array_unique(array_merge(
                $businessOwnerIds,
                BusinessProfile::whereIn('user_id', $trashedOwnerIds)->pluck('user_id')->all()
            )));
        }

        $activeCount = Location::count();
        $trashedCount = Location::onlyTrashed()->count();

        return view('admin.locations.index', compact(
            'locations',
            'categories',
            'sortDir',
            'businessOwnerIds',
            'trash',
            'activeCount',
            'trashedCount'
        ));
    }

    /** Hiển thị form tạo địa điểm mới. */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.locations.create', compact('categories'));
    }

    /** Lưu địa điểm mới, tạo slug duy nhất và nén ảnh đại diện nếu có. */
    public function store(StoreLocationRequest $request)
    {
        $validated = $request->validated();

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

    /** Form chỉnh sửa địa điểm kèm ảnh và panorama hiện có. */
    public function edit(Location $location)
    {
        $categories = Category::where('status', 'active')->get();
        $images = $location->images;
        $panoramas = $location->panoramas;

        $isBusinessLocation = false;
        $businessOwner = null;
        if ($location->created_by) {
            $approvedProfile = BusinessProfile::where('user_id', $location->created_by)
                ->where('status', 'approved')
                ->first();
            if ($approvedProfile) {
                $isBusinessLocation = true;
                $businessOwner = User::find($location->created_by);
            }
        }

        return view('admin.locations.edit', compact(
            'location',
            'categories',
            'images',
            'panoramas',
            'isBusinessLocation',
            'businessOwner'
        ));
    }

    /** Cập nhật địa điểm, tạo lại slug nếu đổi tên và thay ảnh đại diện nếu upload mới. */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        $validated = $request->validated();

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

        return redirect()->route('admin.locations.index')->with('success', 'Cập nhật địa điểm thành công!');
    }

    /**
     * Xóa tạm địa điểm (soft delete): giữ file & quan hệ để khôi phục.
     * Địa điểm DN đã duyệt: bắt buộc lý do, thông báo chủ; nếu là địa điểm cuối → hạ vai trò + từ chối hồ sơ (giữ bản ghi).
     */
    public function destroy(Request $request, Location $location)
    {
        $ownerId = $location->created_by;
        $businessProfile = null;
        if ($ownerId) {
            $businessProfile = BusinessProfile::where('user_id', $ownerId)
                ->where('status', 'approved')
                ->first();
        }
        $isBusinessLocation = (bool) $businessProfile;

        if ($isBusinessLocation) {
            $request->validate([
                'delete_reason' => 'required|string|max:1000',
            ], [
                'delete_reason.required' => 'Vui lòng nhập lý do xóa để thông báo cho doanh nghiệp.',
            ]);
        }

        $deleteReason = trim((string) $request->input('delete_reason', ''));

        try {
            DB::transaction(function () use ($location, $ownerId, $businessProfile, $isBusinessLocation, $deleteReason) {
                if ($isBusinessLocation && $businessProfile) {
                    $hasOtherLocations = Location::where('created_by', $ownerId)
                        ->where('id', '!=', $location->id)
                        ->exists();

                    UserNotification::create([
                        'user_id' => $ownerId,
                        'type' => 'business_location_removed',
                        'title' => 'Địa điểm doanh nghiệp đã bị gỡ tạm',
                        'message' => 'Địa điểm "' . $location->name . '" của bạn đã bị quản gỡ tạm khỏi bản đồ hệ thống.'
                            . "\nLý do: " . $deleteReason
                            . ($hasOtherLocations ? '' : "\nTài khoản doanh nghiệp tạm ngưng.")
                            . "\nNếu cần hỗ trợ, vui lòng liên hệ hotline 1800 400 389 để được tư vấn và hỗ trợ.",
                        'link' => route('client.profile'),
                    ]);

                    if (!$hasOtherLocations) {
                        $owner = User::find($ownerId);
                        if ($owner && $owner->role === 'business') {
                            $owner->update(['role' => 'user']);
                        }
                        $businessProfile->update([
                            'status' => 'rejected',
                            'reject_reason' => Location::BIZ_SOFT_DELETE_REASON_PREFIX
                                . ' Địa điểm "' . $location->name . '" đã bị gỡ khỏi bản đồ hệ thống. Lý do: ' . $deleteReason,
                        ]);
                    }
                }

                $location->delete();
            });

            $msg = $isBusinessLocation
                ? 'Đã xóa địa điểm và gửi thông báo lý do đến doanh nghiệp.'
                : 'Đã xóa địa điểm thành công.';

            return redirect()->route('admin.locations.index', $request->only(['search', 'category_id', 'sort_dir']))
                ->with('success', $msg);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Không thể xóa tạm địa điểm. Vui lòng thử lại.');
        }
    }

    /**
     * Khôi phục địa điểm từ thùng rác.
     * Nếu hồ sơ DN bị từ chối do gỡ tạm địa điểm này → duyệt lại + trả vai trò business.
     */
    public function restore(Request $request, int $id)
    {
        $location = Location::onlyTrashed()->findOrFail($id);

        try {
            DB::transaction(function () use ($location) {
                $location->restore();

                $ownerId = $location->created_by;
                if (!$ownerId) {
                    return;
                }

                $profile = BusinessProfile::where('user_id', $ownerId)->first();
                if (!$profile) {
                    return;
                }

                $reason = (string) ($profile->reject_reason ?? '');
                $wasSoftRemoved = $profile->status === 'rejected'
                    && str_starts_with($reason, Location::BIZ_SOFT_DELETE_REASON_PREFIX);

                if ($wasSoftRemoved) {
                    $profile->update([
                        'status' => 'approved',
                        'reject_reason' => null,
                    ]);

                    $owner = User::find($ownerId);
                    if ($owner && in_array($owner->role, ['user', 'member'], true)) {
                        $owner->update(['role' => 'business']);
                    }

                    UserNotification::create([
                        'user_id' => $ownerId,
                        'type' => 'business_location_restored',
                        'title' => 'Địa điểm doanh nghiệp đã được khôi phục',
                        'message' => 'Địa điểm "' . $location->name . '" đã được quản trị viên khôi phục. Tài khoản doanh nghiệp của bạn đã hoạt động trở lại.',
                        'link' => route('business.dashboard'),
                    ]);
                }
            });

            return redirect()->route('admin.locations.index', ['trash' => 1] + $request->only(['search', 'category_id', 'sort_dir']))
                ->with('success', 'Đã khôi phục địa điểm thành công.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Không thể khôi phục địa điểm. Vui lòng thử lại.');
        }
    }

    /**
     * Xóa vĩnh viễn địa điểm trong thùng rác: dọn file media, cascade quan hệ.
     * Địa điểm DN: bắt buộc lý do nếu hồ sơ còn tồn tại; không tạo lại hồ sơ đã xử lý lúc soft-delete.
     */
    public function forceDestroy(Request $request, int $id)
    {
        $location = Location::onlyTrashed()->with(['images', 'panoramas'])->findOrFail($id);

        $ownerId = $location->created_by;
        $businessProfile = $ownerId
            ? BusinessProfile::where('user_id', $ownerId)->first()
            : null;
        $isBusinessLocation = (bool) $businessProfile;

        if ($isBusinessLocation) {
            $request->validate([
                'delete_reason' => 'required|string|max:1000',
            ], [
                'delete_reason.required' => 'Vui lòng nhập lý do xóa vĩnh viễn để lưu hồ sơ / thông báo.',
            ]);
        }

        $deleteReason = trim((string) $request->input('delete_reason', ''));

        try {
            DB::transaction(function () use ($location, $ownerId, $businessProfile, $isBusinessLocation, $deleteReason) {
                foreach ($location->images as $img) {
                    if ($img->image_url) {
                        Storage::disk('public')->delete($img->image_url);
                    }
                }
                foreach ($location->panoramas as $pano) {
                    if ($pano->image_url) {
                        Storage::disk('public')->delete($pano->image_url);
                    }
                }
                if ($location->thumbnail_url) {
                    Storage::disk('public')->delete($location->thumbnail_url);
                }
                if ($location->audio_url) {
                    Storage::disk('public')->delete($location->audio_url);
                }

                if ($isBusinessLocation && $businessProfile && $ownerId) {
                    UserNotification::create([
                        'user_id' => $ownerId,
                        'type' => 'business_location_removed',
                        'title' => 'Địa điểm doanh nghiệp đã bị xóa vĩnh viễn',
                        'message' => 'Địa điểm "' . $location->name . '" đã bị quản trị viên xóa vĩnh viễn khỏi hệ thống.'
                            . "\nLý do: " . $deleteReason
                            . "\nBạn có thể đăng ký lại tài khoản doanh nghiệp nếu cần."
                            . "\nNếu cần hỗ trợ, vui lòng liên hệ hotline 1800 400 389 (Thứ Hai – Thứ Sáu, 8:00 – 17:30) hoặc gửi góp ý trong mục Hồ sơ.",
                        'link' => route('client.profile'),
                    ]);

                    $hasOtherLocations = Location::withTrashed()
                        ->where('created_by', $ownerId)
                        ->where('id', '!=', $location->id)
                        ->exists();

                    if (!$hasOtherLocations) {
                        $owner = User::find($ownerId);
                        if ($owner && $owner->role === 'business') {
                            $owner->update(['role' => 'user']);
                        }
                        // Hồ sơ có thể đã rejected lúc soft-delete — giữ bản ghi lịch sử, không hard-delete
                        if ($businessProfile->status !== 'rejected') {
                            $businessProfile->update([
                                'status' => 'rejected',
                                'reject_reason' => 'Địa điểm đã bị xóa vĩnh viễn. Lý do: ' . $deleteReason,
                            ]);
                        }
                    }
                }

                $location->forceDelete();
            });

            return redirect()->route('admin.locations.index', ['trash' => 1] + $request->only(['search', 'category_id', 'sort_dir']))
                ->with('success', 'Đã xóa vĩnh viễn địa điểm và dọn dữ liệu liên quan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Không thể xóa vĩnh viễn địa điểm. Vui lòng thử lại.');
        }
    }

    /**
     * Thu hồi quyền quản lý DN: giữ địa điểm trên map, gỡ created_by,
     * hạ hồ sơ DN + role, gửi thông báo. Địa điểm lại có thể được nhận bởi DN khác.
     */
    public function revokeBusiness(Request $request, Location $location)
    {
        $ownerId = $location->created_by;
        if (!$ownerId) {
            return back()->with('error', 'Địa điểm này chưa có chủ doanh nghiệp.');
        }

        $businessProfile = BusinessProfile::where('user_id', $ownerId)
            ->where('status', 'approved')
            ->first();

        if (!$businessProfile) {
            return back()->with('error', 'Không tìm thấy hồ sơ doanh nghiệp đang hoạt động cho địa điểm này.');
        }

        $request->validate([
            'revoke_reason' => 'required|string|max:1000',
        ], [
            'revoke_reason.required' => 'Vui lòng nhập lý do thu hồi quyền quản lý.',
        ]);

        $reason = trim((string) $request->input('revoke_reason'));

        try {
            DB::transaction(function () use ($location, $ownerId, $businessProfile, $reason) {
                $location->update(['created_by' => null]);

                $businessProfile->update([
                    'status' => 'rejected',
                    'reject_reason' => BusinessProfile::BIZ_REVOKED_REASON_PREFIX
                        . ' Thu hồi quyền quản lý địa điểm "' . $location->name . '". Lý do: ' . $reason,
                    'location_id' => null,
                ]);

                $owner = User::find($ownerId);
                if ($owner && $owner->role === 'business') {
                    $stillOwns = Location::where('created_by', $ownerId)->exists();
                    if (!$stillOwns) {
                        $owner->update(['role' => 'user']);
                    }
                }

                UserNotification::create([
                    'user_id' => $ownerId,
                    'type' => 'business_management_revoked',
                    'title' => 'Thu hồi quyền quản lý địa điểm',
                    'message' => 'Quyền quản lý địa điểm "' . $location->name . '" đã bị thu hồi. Địa điểm vẫn hiển thị trên bản đồ.'
                        . "\nLý do: " . $reason
                        . "\nNếu cần hỗ trợ, vui lòng liên hệ hotline 1800 400 389 (Thứ Hai – Thứ Sáu, 8:00 – 17:30) hoặc gửi góp ý trong mục Hồ sơ.",
                    'link' => route('client.profile'),
                ]);
            });

            return redirect()
                ->route('admin.locations.edit', [$location->id] + $request->only(['search', 'category_id', 'sort_dir']))
                ->with('success', 'Đã thu hồi quyền quản lý doanh nghiệp. Địa điểm vẫn còn trên bản đồ.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Không thể thu hồi quyền quản lý. Vui lòng thử lại.');
        }
    }

    /** Upload ảnh cho địa điểm qua Ajax (đã nén). */
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

    /** Xóa một ảnh của địa điểm qua Ajax. */
    public function deleteImage(LocationImage $image)
    {
        Storage::disk('public')->delete($image->image_url);
        $image->delete();
        return response()->json(['success' => true]);
    }

    /** Upload ảnh panorama 360° qua Ajax; ảnh quá lớn (>11K) sẽ được resize để tối ưu. */
    public function uploadPanorama(Request $request, Location $location)
    {
        $request->validate(['file' => 'required|image|max:51200']);
        
        $file = $request->file('file');
        
        // Tăng giới hạn bộ nhớ và thời gian chạy cho ảnh cực lớn (vd panorama 18K)
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '300');
        
        $maxWidth = 11264; // Giới hạn 11K
        $info = getimagesize($file->getRealPath());
        
        if ($info && $info[0] > $maxWidth) {
            $path = $file->hashName('locations/panoramas');
            $absolutePath = storage_path('app/public/' . $path);
            
            // Tạo thư mục nếu chưa tồn tại
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

    /** Xóa một panorama của địa điểm qua Ajax. */
    public function deletePanorama(Panorama $panorama)
    {
        Storage::disk('public')->delete($panorama->image_url);
        $panorama->delete();
        return response()->json(['success' => true]);
    }

    /** Upload file audio thuyết minh cho địa điểm qua Ajax. */
    public function uploadAudio(Request $request, Location $location)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,webm|max:20480',
        ]);

        // Xóa audio cũ nếu có
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

    /** Xóa audio thuyết minh của địa điểm. */
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

    /** Lấy danh sách giọng đọc từ máy chủ VieNeu-TTS. */
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

    /** Gọi máy chủ TTS để tạo audio thuyết minh từ văn bản và lưu vào địa điểm. */
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
