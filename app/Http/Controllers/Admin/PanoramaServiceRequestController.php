<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PanoramaServiceRequest;
use Illuminate\Http\Request;

class PanoramaServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

        $query = PanoramaServiceRequest::with('user')->latest();

        if ($status !== 'all' && array_key_exists($status, PanoramaServiceRequest::statusLabels())) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('place_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('display_name', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => PanoramaServiceRequest::count(),
            'pending' => PanoramaServiceRequest::where('status', 'pending')->count(),
            'contacted' => PanoramaServiceRequest::where('status', 'contacted')->count(),
            'done' => PanoramaServiceRequest::where('status', 'done')->count(),
            'cancelled' => PanoramaServiceRequest::where('status', 'cancelled')->count(),
        ];

        return view('admin.panorama_requests.index', compact('requests', 'status', 'search', 'counts'));
    }

    public function updateStatus(Request $request, PanoramaServiceRequest $panoramaRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,contacted,done,cancelled',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $panoramaRequest->update($data);

        return back()->with('success', 'Đã cập nhật trạng thái yêu cầu tour 360.');
    }
}
