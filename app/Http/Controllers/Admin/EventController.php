<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['location', 'creator'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_text', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured);
        }

        $events = $query->paginate(10)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $locations = Location::where('status', 'published')->orderBy('name')->get();
        return view('admin.events.create', compact('locations'));
    }

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
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:5120',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:active,cancelled,expired,hidden',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['created_by'] = Auth::id();
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $data['featured_image'] = $path;
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Thêm sự kiện thành công!');
    }

    public function edit(Event $event)
    {
        $locations = Location::where('status', 'published')->orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'locations'));
    }

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
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:5120',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:active,cancelled,expired,hidden',
        ]);

        $data = $request->except('featured_image');
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        if ($request->name !== $event->name) {
            $data['slug'] = Str::slug($request->name) . '-' . time();
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($event->featured_image && \Storage::disk('public')->exists($event->featured_image)) {
                \Storage::disk('public')->delete($event->featured_image);
            }
            $path = $request->file('featured_image')->store('events', 'public');
            $data['featured_image'] = $path;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Cập nhật sự kiện thành công!');
    }

    /**
     * Toggle visibility (active <-> hidden)
     */
    public function toggleVisibility(Event $event)
    {
        $event->status = $event->status === 'active' ? 'hidden' : 'active';
        $event->save();

        $label = $event->status === 'active' ? 'hiện' : 'ẩn';
        return back()->with('success', "Đã {$label} sự kiện \"{$event->name}\".");
    }

    public function destroy(Event $event)
    {
        if ($event->featured_image && \Storage::disk('public')->exists($event->featured_image)) {
            \Storage::disk('public')->delete($event->featured_image);
        }
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Xóa sự kiện thành công!');
    }
}
