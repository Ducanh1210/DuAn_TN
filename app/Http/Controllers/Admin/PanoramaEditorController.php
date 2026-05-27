<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Panorama;
use App\Models\PanoramaHotspot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PanoramaEditorController extends Controller
{
    public function index(Location $location)
    {
        return view('admin.locations.editor-360', compact('location'));
    }

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

    public function setInitialView(Request $request, Panorama $panorama)
    {
        $panorama->update([
            'initial_yaw' => $request->yaw,
            'initial_pitch' => $request->pitch,
            'initial_fov' => $request->fov
        ]);
        return response()->json(['success' => true]);
    }

    public function setDefaultScene(Panorama $panorama)
    {
        // Set all other panoramas in the same location to false
        Panorama::where('location_id', $panorama->location_id)->update(['is_default' => false]);
        
        // Set this panorama to true
        $panorama->update(['is_default' => true]);
        
        return response()->json(['success' => true]);
    }

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

    public function deleteHotspot(PanoramaHotspot $hotspot)
    {
        $hotspot->delete();
        return response()->json(['success' => true]);
    }

    public function bulkSave(Request $request)
    {
        \DB::beginTransaction();
        try {
            // 1. Process deletes
            if ($request->has('deletes')) {
                \App\Models\PanoramaHotspot::whereIn('id', $request->deletes)->delete();
            }
            
            // 2. Process updates
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
            
            // 3. Process creates
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

    public function updateSceneName(Request $request, Panorama $panorama)
    {
        $panorama->update(['scene_name' => $request->name]);
        return response()->json(['success' => true]);
    }
}
