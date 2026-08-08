<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Location;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Controller quản trị sự kiện: liệt kê (tìm kiếm, lọc), thêm, sửa, ẩn/hiện và xóa;
 * xử lý nén & lưu ảnh nổi bật.
 */
class EventController extends Controller
{
    public function __construct(private ImageCompressionService $imageCompression)
    {
    }

    /** Danh sách sự kiện có tìm kiếm và lọc theo trạng thái / nổi bật. */
    public function index(Request $request)
    {
        $query = Event::with(['location', 'creator'])->latest();

        // Tìm kiếm theo tên / mô tả / địa điểm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_text', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo sự kiện nổi bật
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured);
        }

        $events = $query->paginate(10)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    /** Form tạo sự kiện (kèm danh sách địa điểm để gắn). */
    public function create()
    {
        $locations = Location::where('status', 'published')->orderBy('name')->get();
        return view('admin.events.create', compact('locations'));
    }

    /** Lưu sự kiện mới: tạo slug, gán người tạo và lưu ảnh nổi bật (nếu có). */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'program' => 'nullable|string',
            'location_text' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:20480',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:active,cancelled,expired,hidden',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = Str::slug($request->name) . '-' . uniqid();
        $data['created_by'] = Auth::id();
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        if ($request->hasFile('featured_image')) {
            $path = $this->imageCompression->compressAndSave($request->file('featured_image'), 'events');
            $data['featured_image'] = $path;
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Thêm sự kiện thành công!');
    }

    /** Form sửa sự kiện. */
    public function edit(Event $event)
    {
        $locations = Location::where('status', 'published')->orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'locations'));
    }

    /** Cập nhật sự kiện: đổi slug khi đổi tên và thay ảnh nổi bật (xóa ảnh cũ). */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'program' => 'nullable|string',
            'location_text' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:20480',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:active,cancelled,expired,hidden',
        ]);

        $data = $request->except('featured_image');
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        if ($request->name !== $event->name) {
            $data['slug'] = Str::slug($request->name) . '-' . uniqid();
        }

        if ($request->hasFile('featured_image')) {
            // Xóa ảnh cũ trước khi lưu ảnh mới
            if ($event->featured_image && \Storage::disk('public')->exists($event->featured_image)) {
                \Storage::disk('public')->delete($event->featured_image);
            }
            $path = $this->imageCompression->compressAndSave($request->file('featured_image'), 'events');
            $data['featured_image'] = $path;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Cập nhật sự kiện thành công!');
    }

    /** Bật/tắt hiển thị sự kiện (active <-> hidden). */
    public function toggleVisibility(Event $event)
    {
        $event->status = $event->status === 'active' ? 'hidden' : 'active';
        $event->save();

        $label = $event->status === 'active' ? 'hiện' : 'ẩn';
        return back()->with('success', "Đã {$label} sự kiện \"{$event->name}\".");
    }

    /** Xóa sự kiện và ảnh nổi bật đi kèm. */
    public function destroy(Event $event)
    {
        if ($event->featured_image && \Storage::disk('public')->exists($event->featured_image)) {
            \Storage::disk('public')->delete($event->featured_image);
        }
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Xóa sự kiện thành công!');
    }
}
