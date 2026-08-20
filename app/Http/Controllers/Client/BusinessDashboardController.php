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
use Illuminate\Validation\ValidationException;

/**
 * Controller bảng điều khiển doanh nghiệp: dành cho chủ doanh nghiệp đã được duyệt,
 * cho phép xem thống kê, cập nhật thông tin và quản lý thư viện ảnh của địa điểm.
 */
class BusinessDashboardController extends Controller
{
    public function __construct(private ImageCompressionService $imageCompression)
    {
    }

    /** Trang tổng quan doanh nghiệp: thông tin, địa điểm, bình luận và thống kê. */
    public function index()
    {
        $user = Auth::user();

        // Chỉ cho vào nếu có hồ sơ doanh nghiệp đã được duyệt
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('category')
            ->first();

        if (!$businessProfile) {
            return redirect()->route('client.profile')
                ->with('error', 'Bạn chưa có tài khoản doanh nghiệp được phê duyệt.');
        }

        // Lấy địa điểm gắn với chủ doanh nghiệp này
        $location = Location::where('created_by', $user->id)
            ->with(['category', 'images'])
            ->first();

        if (!$location) {
            return redirect()->route('client.profile')
                ->with('error', 'Địa điểm doanh nghiệp đã bị gỡ khỏi hệ thống. Vui lòng đăng ký lại hoặc liên hệ quản trị viên.');
        }

        // Thống kê: bình luận gốc, lượt thích, lượt xem, điểm đánh giá
        $comments = Comment::where('location_id', $location->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        $favoritesCount = FavoriteLocation::where('location_id', $location->id)->count();
        $viewsCount = $location->view_count ?? 0;
        $averageRating = Comment::where('location_id', $location->id)
            ->whereNull('parent_id')
            ->where('status', 'visible')
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;

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

    /** Chỉ cập nhật mô tả doanh nghiệp (đồng bộ sang địa điểm). */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $validated = $request->validate([
            'description' => 'nullable|string|max:1000',
        ]);

        $description = $validated['description'] ?? null;

        $businessProfile->update([
            'description' => $description,
        ]);

        $location = Location::where('created_by', $user->id)->first();
        if ($location) {
            $location->update([
                'description' => $description,
            ]);
        }

        return redirect()->route('business.dashboard')
            ->with('success', 'Đã cập nhật mô tả thành công!');
    }

    /** Lưu kênh liên hệ công khai (khác SĐT đăng ký lúc duyệt). */
    public function updateContact(Request $request)
    {
        $user = Auth::user();

        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        try {
            $validated = $request->validate([
            'public_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s().-]{8,20}$/'],
            'zalo' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                $value = trim((string) $value);
                if ($value === '') {
                    return;
                }
                if (preg_match('/^https?:\/\//i', $value)) {
                    if (!preg_match('/^https?:\/\/(www\.)?zalo\.me\//i', $value)) {
                        $fail('Zalo phải là số điện thoại hoặc link zalo.me.');
                    }
                    return;
                }
                if (!preg_match('/^[0-9+\s().-]{8,20}$/', $value)) {
                    $fail('Zalo phải là số điện thoại hoặc link zalo.me.');
                }
            }],
            'facebook' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                $value = trim((string) $value);
                if ($value === '') {
                    return;
                }
                if (preg_match('/youtube\.com|youtu\.be|tiktok\.com|instagram\.com/i', $value)) {
                    $fail('Facebook phải là link facebook.com hoặc fb.com');
                    return;
                }
                if (preg_match('/^https?:\/\//i', $value)) {
                    if (!preg_match('/^https?:\/\/([a-z0-9-]+\.)?(facebook\.com|fb\.com)\//i', $value)) {
                        $fail('Facebook phải là link facebook.com hoặc fb.com.');
                    }
                    return;
                }
                if (!preg_match('/^[A-Za-z0-9._]{3,50}$/', $value)) {
                    $fail('Facebook phải là link facebook.com hoặc tên trang.');
                }
            }],
        ], [
            'public_phone.regex' => 'Số điện thoại khách liên hệ không hợp lệ.',
        ]);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('business.dashboard') . '#tab-contact');
        }

        $businessProfile->update([
            'public_phone' => $validated['public_phone'] ?: null,
            'zalo' => $validated['zalo'] ?: null,
            'facebook' => $validated['facebook'] ?: null,
        ]);

        $location = Location::where('created_by', $user->id)->first();
        if ($location) {
            $location->update([
                'phone' => $businessProfile->public_phone,
                'zalo' => $businessProfile->zalo,
                'facebook' => $businessProfile->facebook,
            ]);
        }

        return redirect()->to(route('business.dashboard') . '#tab-contact')
            ->with('success', 'Đã lưu thông tin liên hệ cho khách.');
    }

    /** Tải ảnh lên thư viện doanh nghiệp và thêm vào ảnh địa điểm. */
    public function uploadPhoto(Request $request)
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $photos = $businessProfile->menu_photos ?? [];
        $legacyStorefrontPhotos = $businessProfile->storefront_photos ?? [];
        $galleryTotal = count($photos) + count($legacyStorefrontPhotos);

        if ($galleryTotal >= 20) {
            return redirect()->route('business.dashboard')
                ->with('error', 'Bạn chỉ có thể tải tối đa 20 ảnh. Vui lòng xóa bớt ảnh cũ trước khi thêm ảnh mới.');
        }

        $request->validate([
            'photo' => 'required|image|max:20480',
        ]);

        $path = $this->imageCompression->compressAndSave($request->file('photo'), 'business_photos');
        $photos[] = $path;
        $businessProfile->update(['menu_photos' => array_values($photos)]);

        // Đồng thời thêm vào thư viện ảnh của địa điểm (nếu có)
        $location = Location::where('created_by', $user->id)->first();
        if ($location) {
            LocationImage::create([
                'location_id' => $location->id,
                'image_url' => $path,
                'caption' => 'Hình ảnh địa điểm',
                'is_thumbnail' => false,
                'sort_order' => 99,
            ]);
        }

        return redirect()->route('business.dashboard')
            ->with('success', 'Đã tải lên hình ảnh mới thành công!');
    }

    /** Xóa một ảnh khỏi thư viện doanh nghiệp (đồng thời xóa file và ảnh địa điểm liên quan). */
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

    /** Chủ DN trả lời / cập nhật trả lời cho một bình luận trên địa điểm của họ. */
    public function replyComment(Request $request, Comment $comment)
    {
        $user = Auth::user();

        BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $location = Location::where('created_by', $user->id)->firstOrFail();

        if ($comment->location_id !== $location->id || $comment->parent_id) {
            abort(403, 'Không thể trả lời bình luận này.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Vui lòng nhập nội dung trả lời.',
        ]);

        $existing = Comment::where('parent_id', $comment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->update([
                'content' => $validated['content'],
                'status' => 'visible',
            ]);
            $message = 'Đã cập nhật câu trả lời.';
        } else {
            Comment::create([
                'user_id' => $user->id,
                'location_id' => $location->id,
                'parent_id' => $comment->id,
                'content' => $validated['content'],
                'rating' => null,
                'status' => 'visible',
            ]);
            $message = 'Đã gửi trả lời.';
        }

        return redirect()->to(route('business.dashboard') . '#tab-reviews')
            ->with('success', $message);
    }

    /** Thu hồi (xóa) câu trả lời của chủ DN. */
    public function deleteReply(Comment $comment)
    {
        $user = Auth::user();

        BusinessProfile::where('user_id', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $location = Location::where('created_by', $user->id)->firstOrFail();

        if ($comment->location_id !== $location->id || $comment->parent_id) {
            abort(403, 'Không thể thu hồi phản hồi này.');
        }

        $deleted = Comment::where('parent_id', $comment->id)
            ->where('user_id', $user->id)
            ->delete();

        if (!$deleted) {
            return redirect()->to(route('business.dashboard') . '#tab-reviews')
                ->with('error', 'Không tìm thấy câu trả lời để thu hồi.');
        }

        return redirect()->to(route('business.dashboard') . '#tab-reviews')
            ->with('success', 'Đã thu hồi câu trả lời.');
    }
}
