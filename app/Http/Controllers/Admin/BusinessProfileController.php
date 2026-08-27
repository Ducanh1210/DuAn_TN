<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Location;
use App\Models\LocationImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Controller quản trị yêu cầu nâng cấp doanh nghiệp: liệt kê (lọc/tìm kiếm), xem chi tiết,
 * phê duyệt (tự tạo địa điểm trên bản đồ + ảnh công khai) hoặc từ chối kèm lý do.
 */
class BusinessProfileController extends Controller
{
    /** Danh sách yêu cầu nâng cấp doanh nghiệp, có lọc theo trạng thái và tìm kiếm. */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = BusinessProfile::with(['user', 'category'])->latest();

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address_city', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uQuery) use ($search) {
                      $uQuery->where('username', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('display_name', 'like', "%{$search}%");
                  });
            });
        }

        $businessProfiles = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => BusinessProfile::count(),
            'pending' => BusinessProfile::where('status', 'pending')->count(),
            'approved' => BusinessProfile::where('status', 'approved')->count(),
            'rejected' => BusinessProfile::where('status', 'rejected')->count(),
        ];

        return view('admin.business.index', compact('businessProfiles', 'status', 'search', 'counts'));
    }

    /** Chi tiết một yêu cầu nâng cấp doanh nghiệp. */
    public function show($id)
    {
        $businessProfile = BusinessProfile::with(['user', 'category', 'claimedLocation'])->findOrFail($id);

        return view('admin.business.show', compact('businessProfile'));
    }

    /**
     * Phê duyệt yêu cầu: đổi trạng thái, nâng vai trò người dùng lên 'business' và
     * nhận địa điểm có sẵn (claim) hoặc tự tạo địa điểm trên bản đồ nếu chưa có.
     */
    public function approve(Request $request, $id)
    {
        $businessProfile = BusinessProfile::with(['user', 'claimedLocation'])->findOrFail($id);

        // Nhận địa điểm đã có trên map
        if ($businessProfile->location_id) {
            $claimLoc = Location::withTrashed()->find($businessProfile->location_id);
            if (!$claimLoc) {
                return back()->with('error', 'Địa điểm được yêu cầu nhận quyền không còn tồn tại.');
            }
            if ($claimLoc->trashed()) {
                $claimLoc->restore();
            }
            if (!BusinessProfile::isLocationClaimable($claimLoc) && (int) $claimLoc->created_by !== (int) $businessProfile->user_id) {
                return back()->with('error', 'Địa điểm này đã thuộc doanh nghiệp khác. Không thể phê duyệt nhận quyền.');
            }

            $businessProfile->update([
                'status' => 'approved',
                'reject_reason' => null,
            ]);

            if ($businessProfile->user && in_array($businessProfile->user->role, ['user', 'member'], true)) {
                $businessProfile->user->update(['role' => 'business']);
            }

            $claimLoc->update([
                'created_by' => $businessProfile->user_id,
                'status' => 'published',
                'phone' => $businessProfile->public_phone ?: $businessProfile->phone ?: $claimLoc->phone,
                'zalo' => $businessProfile->zalo ?: $claimLoc->zalo,
                'facebook' => $businessProfile->facebook ?: $claimLoc->facebook,
            ]);

            return redirect()->route('admin.business-profiles.index')
                ->with('success', "Đã phê duyệt nhận quyền địa điểm \"{$claimLoc->name}\" cho doanh nghiệp \"{$businessProfile->business_name}\".");
        }

        $businessProfile->update([
            'status' => 'approved',
            'reject_reason' => null,
        ]);

        // Nâng vai trò lên 'business' nếu người dùng đang là thành viên thường
        if ($businessProfile->user && in_array($businessProfile->user->role, ['user', 'member'])) {
            $businessProfile->user->update(['role' => 'business']);
        }

        // Tự tạo / khôi phục địa điểm trên bản đồ nếu chưa có (hoặc đang trong thùng rác)
        $existingLoc = Location::withTrashed()
            ->where('created_by', $businessProfile->user_id)
            ->first();

        if ($existingLoc && $existingLoc->trashed()) {
            $existingLoc->restore();
            if ($existingLoc->status !== 'published') {
                $existingLoc->update(['status' => 'published']);
            }
        } elseif (!$existingLoc) {
            $slug = Str::slug($businessProfile->business_name) . '-' . time();
            $fullAddress = trim($businessProfile->address_street . ', ' . $businessProfile->address_city . ', ' . $businessProfile->address_province);

            $thumbnail = null;
            if (!empty($businessProfile->avatar_photo)) {
                $thumbnail = $businessProfile->avatar_photo;
            } elseif (!empty($businessProfile->storefront_photos) && is_array($businessProfile->storefront_photos)) {
                $thumbnail = $businessProfile->storefront_photos[0];
            } elseif (!empty($businessProfile->menu_photos) && is_array($businessProfile->menu_photos)) {
                $thumbnail = $businessProfile->menu_photos[0];
            }

            $location = Location::create([
                'category_id' => $businessProfile->category_id,
                'name' => $businessProfile->business_name,
                'slug' => $slug,
                'short_description' => Str::limit($businessProfile->description, 150),
                'description' => $businessProfile->description,
                'address' => $fullAddress,
                'district' => $businessProfile->address_city,
                'province' => $businessProfile->address_province,
                'lat' => $businessProfile->lat,
                'lng' => $businessProfile->lng,
                'phone' => $businessProfile->public_phone,
                'zalo' => $businessProfile->zalo,
                'facebook' => $businessProfile->facebook,
                'website_url' => $businessProfile->website,
                'thumbnail_url' => $thumbnail,
                'status' => 'published',
                'created_by' => $businessProfile->user_id,
            ]);

            // Chỉ lưu ảnh công khai (KHÔNG lưu giấy tờ sở hữu / ảnh xác minh riêng tư)
            $allPhotos = array_merge(
                $businessProfile->avatar_photo ? [$businessProfile->avatar_photo] : [],
                $businessProfile->storefront_photos ?? [],
                $businessProfile->menu_photos ?? []
            );
            $allPhotos = array_values(array_unique(array_filter($allPhotos)));
            foreach ($allPhotos as $index => $photoPath) {
                LocationImage::create([
                    'location_id' => $location->id,
                    'image_url' => $photoPath,
                    'caption' => $businessProfile->business_name,
                    'is_thumbnail' => ($photoPath === $thumbnail),
                    'sort_order' => $index,
                ]);
            }

            $businessProfile->update(['location_id' => $location->id]);
        }

        return redirect()->route('admin.business-profiles.index')
            ->with('success', "Đã phê duyệt yêu cầu nâng cấp doanh nghiệp \"{$businessProfile->business_name}\" thành công!");
    }

    /** Từ chối yêu cầu nâng cấp doanh nghiệp kèm lý do (bắt buộc). */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối yêu cầu.',
        ]);

        $businessProfile = BusinessProfile::findOrFail($id);

        $businessProfile->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
        ]);

        return redirect()->route('admin.business-profiles.index')
            ->with('success', "Đã từ chối yêu cầu của doanh nghiệp \"{$businessProfile->business_name}\".");
    }
}
