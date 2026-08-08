<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Comment;
use App\Models\FavoriteLocation;
use App\Models\Location;
use App\Models\LocationImage;
use App\Models\PanoramaServiceRequest;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessDashboardController extends Controller
{
    public function __construct(private ImageCompressionService $imageCompression)
    {
    }

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

        if (!$location) {
            return redirect()->route('client.profile')
                ->with('error', 'Địa điểm doanh nghiệp đã bị gỡ khỏi hệ thống. Vui lòng đăng ký lại hoặc liên hệ quản trị viên.');
        }

        // Get statistics
        $comments = Comment::where('location_id', $location->id)
            ->with('user')
            ->latest()
            ->get();

        $favoritesCount = FavoriteLocation::where('location_id', $location->id)->count();
        $viewsCount = $location->view_count ?? 0;
        $averageRating = $location->average_rating ?? 0;

        $panoramaServiceRequests = PanoramaServiceRequest::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
        $hasPendingPanoRequest = $panoramaServiceRequests->contains(fn ($r) => $r->status === 'pending');

        return view('client.business.dashboard', compact(
            'businessProfile',
            'location',
            'comments',
            'favoritesCount',
            'viewsCount',
            'averageRating',
            'panoramaServiceRequests',
            'hasPendingPanoRequest'
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
            'photo' => 'required|image|max:20480',
            'type' => 'required|in:storefront,menu',
        ]);

        $path = $this->imageCompression->compressAndSave($request->file('photo'), 'business_photos');

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

    /**
     * Delete a photo from business gallery.
     */
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|in:storefront,menu',
            'index' => 'required|integer|min:0',
        ]);

        $field = $validated['type'] === 'storefront' ? 'storefront_photos' : 'menu_photos';
        $photos = $businessProfile->{$field} ?? [];

        if (!isset($photos[$validated['index']])) {
            return back()->with('error', 'Không tìm thấy ảnh cần xóa.');
        }

        $removedPath = $photos[$validated['index']];
        unset($photos[$validated['index']]);
        $businessProfile->update([$field => array_values($photos)]);

        if ($removedPath && Storage::disk('public')->exists($removedPath)) {
            Storage::disk('public')->delete($removedPath);
        }

        $location = Location::where('created_by', $user->id)->first();
        if ($location) {
            LocationImage::where('location_id', $location->id)
                ->where('image_url', $removedPath)
                ->delete();
        }

        return redirect()->route('business.dashboard')
            ->with('success', 'Đã xóa ảnh thành công.');
    }
}
