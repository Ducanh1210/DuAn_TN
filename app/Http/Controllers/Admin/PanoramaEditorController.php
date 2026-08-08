<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Panorama;
use App\Models\PanoramaHotspot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller trình chỉnh sửa tour 360°: quản lý scene panorama, góc nhìn mặc định,
 * scene mặc định và các hotspot (điểm liên kết/thông tin) trên từng panorama.
 */
class PanoramaEditorController extends Controller
{
    /** Mở trang trình chỉnh sửa 360° cho một địa điểm. */
    public function index(Location $location)
    {
        return view('admin.locations.editor-360', compact('location'));
    }

    /** Trả về JSON toàn bộ dữ liệu panorama + hotspot cho trình chỉnh sửa phía client. */
    public function getData(Location $location)
    {
        $panoramas = $location->panoramas()->with('hotspots')->orderByDesc('is_default')->orderBy('sort_order')->get();
        return response()->json([
            'panoramas' => $panoramas->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->scene_name,
                    'url' => asset('storage/' . $p->image_url),
                    'initialView' => [
                        'yaw' => (float) $p->initial_yaw,
                        'pitch' => (float) $p->initial_pitch,
                        'fov' => (float) $p->initial_fov,
                    ],
                    'hotspots' => $p->hotspots->map(function($h) {
                        return [
                            'id' => $h->id,
                            'type' => $h->hotspot_type,
                            'yaw' => (float) $h->yaw,
                            'pitch' => (float) $h->pitch,
                            'title' => $h->title,
                            'content' => $h->content,
                            'target' => $h->target_panorama_id,
                            'link_url' => $h->link_url,
                        ];
                    })
                ];
            })
        ]);
    }

    /** Lưu góc nhìn ban đầu (yaw/pitch/fov) cho một panorama. */
    public function setInitialView(Request $request, Panorama $panorama)
    {
        $panorama->update([
            'initial_yaw' => $request->yaw,
            'initial_pitch' => $request->pitch,
            'initial_fov' => $request->fov
        ]);
        return response()->json(['success' => true]);
    }

    /** Đặt panorama làm scene mặc định (bỏ mặc định ở tất cả scene khác cùng địa điểm). */
    public function setDefaultScene(Panorama $panorama)
    {
        // Bỏ cờ mặc định ở tất cả panorama khác cùng địa điểm
        Panorama::where('location_id', $panorama->location_id)->update(['is_default' => false]);
        
        // Đặt panorama này làm mặc định
        $panorama->update(['is_default' => true]);
        
        return response()->json(['success' => true]);
    }

    /** Thêm một hotspot mới vào panorama. */
    public function addHotspot(Request $request, Panorama $panorama)
    {
        $hotspot = $panorama->hotspots()->create([
            'hotspot_type' => $request->type,
            'yaw' => $request->yaw,
            'pitch' => $request->pitch,
            'title' => $request->title,
            'content' => $request->content,
            'target_panorama_id' => $request->target,
            'target_yaw' => $request->target_yaw,
            'target_pitch' => $request->target_pitch,
            'link_url' => $request->link_url,
            'scale' => $request->scale ?? 1.0,
        ]);
        return response()->json(['success' => true, 'hotspot' => $hotspot]);
    }

    /** Cập nhật một phần thông tin hotspot (chỉ các trường được gửi lên). */
    public function updateHotspot(Request $request, PanoramaHotspot $hotspot)
    {
        $data = [];
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('content')) $data['content'] = $request->content;
        if ($request->has('target')) $data['target_panorama_id'] = $request->target;
        if ($request->has('link_url')) $data['link_url'] = $request->link_url;
        if ($request->has('yaw')) $data['yaw'] = $request->yaw;
        if ($request->has('pitch')) $data['pitch'] = $request->pitch;
        if ($request->has('scale')) $data['scale'] = $request->scale;
        if ($request->has('target_yaw')) $data['target_yaw'] = $request->target_yaw;
        if ($request->has('target_pitch')) $data['target_pitch'] = $request->target_pitch;
        
        $hotspot->update($data);
        return response()->json(['success' => true]);
    }

    /** Xóa một hotspot. */
    public function deleteHotspot(PanoramaHotspot $hotspot)
    {
        $hotspot->delete();
        return response()->json(['success' => true]);
    }

    /** Lưu hàng loạt thay đổi hotspot (xóa/sửa/thêm) trong một giao dịch. */
    public function bulkSave(Request $request)
    {
        \DB::beginTransaction();
        try {
            // 1. Xử lý xóa
            if ($request->has('deletes')) {
                \App\Models\PanoramaHotspot::whereIn('id', $request->deletes)->delete();
            }
            
            // 2. Xử lý cập nhật
            if ($request->has('updates')) {
                foreach ($request->updates as $item) {
                    $hotspot = \App\Models\PanoramaHotspot::find($item['id']);
                    if ($hotspot) {
                        $data = [];
                        if (array_key_exists('title', $item)) $data['title'] = $item['title'];
                        if (array_key_exists('content', $item)) $data['content'] = $item['content'];
                        if (array_key_exists('target', $item)) $data['target_panorama_id'] = $item['target'] ?: null;
                        if (array_key_exists('yaw', $item)) $data['yaw'] = $item['yaw'];
                        if (array_key_exists('pitch', $item)) $data['pitch'] = $item['pitch'];
                        if (array_key_exists('scale', $item)) $data['scale'] = $item['scale'];
                        if (array_key_exists('target_yaw', $item)) $data['target_yaw'] = $item['target_yaw'];
                        if (array_key_exists('target_pitch', $item)) $data['target_pitch'] = $item['target_pitch'];
                        
                        $hotspot->update($data);
                    }
                }
            }
            
            // 3. Xử lý tạo mới
            if ($request->has('creates')) {
                foreach ($request->creates as $item) {
                    $panorama = \App\Models\Panorama::find($item['sceneId']);
                    if ($panorama) {
                        $panorama->hotspots()->create([
                            'hotspot_type' => $item['type'],
                            'yaw' => $item['yaw'],
                            'pitch' => $item['pitch'],
                            'title' => $item['title'] ?? '',
                            'content' => $item['content'] ?? '',
                            'target_panorama_id' => $item['target'] ?: null,
                            'target_yaw' => $item['target_yaw'] ?? null,
                            'target_pitch' => $item['target_pitch'] ?? null,
                            'scale' => $item['scale'] ?? 1.0,
                        ]);
                    }
                }
            }
            
            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Đổi tên hiển thị của một scene panorama. */
    public function updateSceneName(Request $request, Panorama $panorama)
    {
        $panorama->update(['scene_name' => $request->name]);
        return response()->json(['success' => true]);
    }
}
