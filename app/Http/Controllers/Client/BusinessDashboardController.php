<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Comment;
use App\Models\FavoriteLocation;
use App\Models\Location;
use App\Models\LocationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessDashboardController extends Controller
{
    /**
     * Display the business dashboard for approved business owners.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user has an approved business profile
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('category')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('client.profile')
                ->with('error', 'Bạn chưa có tài khoản doanh nghiệp được phê duyệt.');
        }

        // Get associated location
        $location = Location::where('created_by', $user->id)
            ->with(['category', 'images'])
            ->first();

        // Get statistics
        $comments = collect();
        $favoritesCount = 0;
        $viewsCount = 0;
        $averageRating = 0;

        if ($location) {
            $comments = Comment::where('location_id', $location->id)
                ->with('user')
                ->latest()
                ->get();

            $favoritesCount = FavoriteLocation::where('location_id', $location->id)->count();
            $viewsCount = $location->view_count ?? 0;
            $averageRating = $location->average_rating ?? 0;
        }

        return view('client.business.dashboard', compact(
            'businessProfile',
            'location',
            'comments',
            'favoritesCount',
            'viewsCount',
            'averageRating'
        ));
    }

    /**
     * Update business information from the dashboard.
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
        ], [
            'business_name.required' => 'Vui lòng nhập tên doanh nghiệp.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'address_street.required' => 'Vui lòng nhập địa chỉ đường phố.',
            'address_city.required' => 'Vui lòng nhập thành phố/huyện.',
        ]);

        $businessProfile->update([
            'business_name' => $validated['business_name'],
            'phone' => $validated['phone'],
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
            'address_street' => $validated['address_street'],
            'address_city' => $validated['address_city'],
        ]);

        // Also update associated location record if it exists
        $location = Location::where('created_by', $user->id)->first();
        if ($location) {
            $fullAddress = trim($validated['address_street'] . ', ' . $validated['address_city'] . ', ' . $businessProfile->address_province);
            $location->update([
                'name' => $validated['business_name'],
                'phone' => $validated['phone'],
                'website_url' => $validated['website'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $fullAddress,
                'district' => $validated['address_city'],
            ]);
        }

        return redirect()->route('business.dashboard')
            ->with('success', 'Đã cập nhật thông tin doanh nghiệp thành công!');
    }

    /**
     * Upload photo to business gallery.
     */
    public function uploadPhoto(Request $request)
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $request->validate([
            'photo' => 'required|image|max:5120',
            'type' => 'required|in:storefront,menu',
        ]);

        $file = $request->file('photo');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('business_photos', $filename, 'public');

        if ($request->type === 'storefront') {
            $photos = $businessProfile->storefront_photos ?? [];
            $photos[] = $path;
            $businessProfile->update(['storefront_photos' => array_values($photos)]);
        } else {
            $photos = $businessProfile->menu_photos ?? [];
            $photos[] = $path;
            $businessProfile->update(['menu_photos' => array_values($photos)]);
        }

        // Also add to location images if location exists
        $location = Location::where('created_by', $user->id)->first();
        if ($location) {
            LocationImage::create([
                'location_id' => $location->id,
                'image_url' => $path,
                'caption' => $request->type === 'storefront' ? 'Mặt tiền cửa hàng' : 'Thực đơn / Dịch vụ',
                'is_thumbnail' => false,
                'sort_order' => 99,
            ]);
        }

        return redirect()->route('business.dashboard')
            ->with('success', 'Đã tải lên hình ảnh mới thành công!');
    }
}
