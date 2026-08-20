<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller quản trị đóng góp của người dùng: danh sách đề xuất địa điểm,
 * xem chi tiết (kèm địa điểm lân cận) và cập nhật trạng thái xử lý.
 */
class ContributionController extends Controller
{
    /** Danh sách đề xuất địa điểm; tab "feedbacks" chuyển sang trang báo cáo/góp ý. */
    public function index(Request $request)
    {
        if ($request->get('tab') === 'feedbacks') {
            return redirect()->route('admin.reports.index', ['tab' => 'feedbacks']);
        }

        $suggestionsQuery = LocationSuggestion::with('user')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $suggestionsQuery->where('status', $request->status);
        }

        $suggestions = $suggestionsQuery->paginate(15)->withQueryString();
        $pendingSuggestions = LocationSuggestion::where('status', 'pending')->count();

        return view('admin.contributions.index', compact('suggestions', 'pendingSuggestions'));
    }

    /** Chi tiết một đề xuất; nếu có tọa độ thì truy vấn các địa điểm trong bán kính 3km để đối chiếu trùng lặp. */
    public function showSuggestion($id)
    {
        $suggestion = LocationSuggestion::with('user', 'processor')->findOrFail($id);

        $nearbyLocations = collect();
        if ($suggestion->lat && $suggestion->lng) {
            // Tính khoảng cách bằng công thức Haversine ngay trong SQL để tìm điểm gần
            $lat = (float) $suggestion->lat;
            $lng = (float) $suggestion->lng;

            $nearbyLocations = \App\Models\Location::query()
                ->select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance_km',
                    [$lat, $lng, $lat]
                )
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->having('distance_km', '<=', 3)
                ->orderBy('distance_km')
                ->limit(8)
                ->get();
        }

        $nearbyMapData = $nearbyLocations->map(function ($loc) {
            return [
                'name' => $loc->name,
                'lat' => (float) $loc->lat,
                'lng' => (float) $loc->lng,
                'distance_km' => round((float) $loc->distance_km, 2),
            ];
        })->values();

        return view('admin.contributions.show_suggestion', compact('suggestion', 'nearbyLocations', 'nearbyMapData'));
    }

    /** Cập nhật trạng thái xử lý và ghi chú của quản trị cho một đề xuất. */
    public function updateSuggestion(Request $request, $id)
    {
        $suggestion = LocationSuggestion::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,need_more_info',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $suggestion->status = $request->status;
        $suggestion->admin_note = $request->admin_note;
        $suggestion->processed_by = auth()->id();
        $suggestion->processed_at = now();
        $suggestion->save();

        return redirect()
            ->route('admin.contributions.suggestions.show', $suggestion->id)
            ->with('success', 'Đã cập nhật đóng góp.');
    }

    /** Xóa một đề xuất địa điểm và ảnh đính kèm. */
    public function destroySuggestion($id)
    {
        $suggestion = LocationSuggestion::findOrFail($id);

        foreach ($suggestion->images ?? [] as $imagePath) {
            $relative = ltrim(str_replace('storage/', '', (string) $imagePath), '/');
            if ($relative !== '') {
                Storage::disk('public')->delete($relative);
            }
        }

        $suggestion->delete();

        return redirect()
            ->route('admin.contributions.index', ['tab' => 'suggestions'])
            ->with('success', 'Đã xóa đề xuất địa điểm.');
    }
}
