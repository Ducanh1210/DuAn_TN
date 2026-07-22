<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationSuggestion;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\UserController; // Để cộng điểm

class LocationSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = LocationSuggestion::with('user', 'processor')->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $suggestions = $query->paginate(15);
        return view('admin.location_suggestions.index', compact('suggestions'));
    }

    public function show($id)
    {
        $suggestion = LocationSuggestion::with('user', 'processor', 'createdLocation')->findOrFail($id);
        return view('admin.location_suggestions.show', compact('suggestion'));
    }

    public function update(Request $request, $id)
    {
        $suggestion = LocationSuggestion::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,need_more_info',
            'admin_note' => 'nullable|string',
            'reject_reason' => 'nullable|string',
        ]);

        $suggestion->status = $request->status;
        $suggestion->admin_note = $request->admin_note;
        $suggestion->reject_reason = $request->reject_reason;
        $suggestion->processed_by = auth()->id();
        $suggestion->processed_at = now();

        $suggestion->save();

        return redirect()->route('admin.location_suggestions.show', $suggestion->id)
                         ->with('success', 'Đã cập nhật trạng thái đề xuất thành công.');
    }

    public function approve(Request $request, $id)
    {
        $suggestion = LocationSuggestion::findOrFail($id);

        if ($suggestion->status === 'approved' && $suggestion->created_location_id) {
            return back()->with('error', 'Đề xuất này đã được duyệt và tạo địa điểm.');
        }

        DB::beginTransaction();
        try {
            // Tạo Location mới từ dữ liệu đề xuất
            $location = new Location();
            $location->name = $suggestion->name;
            $location->address = $suggestion->address;
            $location->description = $suggestion->description;
            $location->latitude = $suggestion->lat;
            $location->longitude = $suggestion->lng;
            $location->status = 'draft'; // Mặc định draft để Admin điền thêm thông tin (như category)
            $location->save();

            // Lưu ảnh nếu có
            if ($suggestion->images && is_array($suggestion->images)) {
                foreach ($suggestion->images as $index => $imageUrl) {
                    $location->images()->create([
                        'image_url' => $imageUrl,
                        'is_thumbnail' => $index === 0 ? true : false,
                    ]);
                }
            }

            // Cập nhật trạng thái suggestion
            $suggestion->status = 'approved';
            $suggestion->created_location_id = $location->id;
            $suggestion->processed_by = auth()->id();
            $suggestion->processed_at = now();
            $suggestion->save();

            // Cộng điểm cho User đóng góp (ví dụ: +50 điểm)
            if ($suggestion->user_id) {
                $user = \App\Models\User::find($suggestion->user_id);
                if ($user) {
                    \App\Services\PointService::awardPoints($user, 50, 'location_suggestion_approved', 'Đề xuất địa điểm mới được duyệt: ' . $suggestion->name);
                }
            }

            DB::commit();

            return redirect()->route('admin.locations.edit', $location->id)
                             ->with('success', 'Đã duyệt đề xuất và tạo địa điểm. Vui lòng bổ sung thêm danh mục và thông tin khác.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
