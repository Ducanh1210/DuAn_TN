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

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('category')->latest()->paginate(10);
        return view('admin.locations.index', compact('locations'));
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
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $location = Location::create($data);

        return redirect()->route('admin.locations.edit', $location->id)->with('success', 'Thêm địa điểm thành công! Vui lòng tiếp tục cập nhật hình ảnh và 360.');
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
        ]);

        $data = $request->all();
        if ($request->name !== $location->name) {
            $data['slug'] = Str::slug($request->name) . '-' . time();
        }
        $data['updated_by'] = Auth::id();

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
        return redirect()->route('admin.locations.index')->with('success', 'Xóa địa điểm thành công!');
    }

    // Ajax Image Upload
    public function uploadImage(Request $request, Location $location)
    {
        $request->validate(['file' => 'required|image|max:5120']);
        $path = $request->file('file')->store('locations/images', 'public');

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
        $request->validate(['file' => 'required|image|max:10240']);
        $path = $request->file('file')->store('locations/panoramas', 'public');

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
        $location->update(['audio_url' => $path]);

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

        $location->update(['audio_url' => null]);

        return response()->json(['success' => true]);
    }
}
