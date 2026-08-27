<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Comment;
use App\Models\Location;
use App\Models\Category;
use App\Models\BusinessProfile;
use App\Models\Mission;
use App\Models\UserMission;
use App\Models\AvatarFrame;
use App\Models\UserAvatarFrame;
use App\Models\Reward;
use App\Models\UserRedemption;
use App\Models\PanoramaServiceRequest;
use App\Services\MissionService;
use App\Services\RewardService;
use Illuminate\Support\Str;


/**
 * Controller trang cá nhân người dùng: thông tin/cài đặt tài khoản, yêu thích, bình luận,
 * thông báo, đăng ký doanh nghiệp, điểm thưởng, nhiệm vụ và khung avatar.
 */
class ProfileController extends Controller
{
    /** Trang cá nhân + bảng cài đặt: gom dữ liệu yêu thích, bình luận, điểm, nhiệm vụ, thông báo. */
    public function index()
    {
        $user = Auth::user();
        
        // Chỉ hiển thị địa điểm còn tồn tại và đang published
        $favorites = $user->favorites()
            ->where('locations.status', 'published')
            ->with(['category', 'images'])
            ->get();
            
        // Chuẩn hóa URL ảnh đại diện cho các địa điểm yêu thích
        $favorites->each(function($loc) {
            if ($loc->category && $loc->category->icon) {
                $loc->category->icon_url = asset($loc->category->icon);
            }
            if ($loc->thumbnail_url && !str_starts_with($loc->thumbnail_url, 'http')) {
                $loc->thumbnail_url = asset('storage/' . ltrim($loc->thumbnail_url, '/'));
            } elseif ($loc->images && $loc->images->count() > 0) {
                $thumbnail = $loc->images->where('is_thumbnail', true)->first() ?? $loc->images->first();
                $loc->thumbnail_url = !str_starts_with($thumbnail->image_url, 'http') ? asset('storage/' . ltrim($thumbnail->image_url, '/')) : $thumbnail->image_url;
            } else {
                $loc->thumbnail_url = 'https://placehold.co/600x400?text=' . urlencode($loc->name);
            }
        });

        // Load comments with locations (bỏ bình luận của địa điểm đã xóa)
        $comments = $user->comments()
            ->whereHas('location')
            ->with('location')
            ->get();

        // Tải danh mục và hồ sơ doanh nghiệp
        $categories = Category::where('status', 'active')->get();
        $businessProfile = BusinessProfile::where('user_id', $user->id)->with('category')->first();

        // Tải thông báo (vd: địa điểm doanh nghiệp bị gỡ) rồi đánh dấu các thông báo chưa đọc là đã đọc
        $notifications = \App\Models\UserNotification::where('user_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();
        \App\Models\UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Tải danh sách khung avatar và các khung người dùng đã mở khóa
        $allFrames = AvatarFrame::where('status', 'active')->orderBy('required_points', 'asc')->get();
        $unlockedFrameIds = UserAvatarFrame::where('user_id', $user->id)
            ->pluck('avatar_frame_id')
            ->toArray();

        $itineraries = $user->itineraries()->get();

        $panoramaServiceRequests = PanoramaServiceRequest::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
        $hasPendingPanoRequest = $panoramaServiceRequests->contains(fn ($r) => $r->status === 'pending');

        // Lịch sử điểm: gộp các bản ghi active_session theo từng phút thành tóm tắt theo ngày cho gọn
        $rawPointTx = \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('amount', '!=', 0)
            ->orderByDesc('created_at')
            ->get();

        $pointTxTotal = $rawPointTx->count();
        $sessionBuckets = [];
        $pointHistory = [];

        foreach ($rawPointTx as $tx) {
            if ($tx->action === 'active_session') {
                $dayKey = $tx->created_at->format('Y-m-d');
                if (!isset($sessionBuckets[$dayKey])) {
                    $sessionBuckets[$dayKey] = [
                        'key' => 'session-' . $dayKey,
                        'action' => 'active_session',
                        'filter' => 'session',
                        'amount' => 0,
                        'count' => 0,
                        'created_at' => $tx->created_at,
                        'description' => '',
                        'aggregated' => true,
                    ];
                }
                $sessionBuckets[$dayKey]['amount'] += (int) $tx->amount;
                $sessionBuckets[$dayKey]['count']++;
                if ($tx->created_at->gt($sessionBuckets[$dayKey]['created_at'])) {
                    $sessionBuckets[$dayKey]['created_at'] = $tx->created_at;
                }
                continue;
            }

            $filter = match ($tx->action) {
                'daily_login' => 'daily',
                'comment' => 'comment',
                'favorite' => 'favorite',
                'mission_reward' => 'mission',
                default => 'other',
            };

            $pointHistory[] = [
                'key' => 'tx-' . $tx->id,
                'action' => $tx->action,
                'filter' => $filter,
                'amount' => (int) $tx->amount,
                'count' => 1,
                'created_at' => $tx->created_at,
                'description' => $tx->description,
                'aggregated' => false,
            ];
        }

        foreach ($sessionBuckets as $dayKey => $bucket) {
            $bucket['description'] = 'Online ' . $bucket['count'] . ' phút · đã gộp ' . $bucket['count'] . ' bản ghi trong ngày '
                . \Carbon\Carbon::parse($dayKey)->format('d/m/Y');
            $pointHistory[] = $bucket;
        }

        usort($pointHistory, function ($a, $b) {
            return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
        });

        return view('client.profile', compact(
            'user',
            'favorites',
            'comments',
            'categories',
            'businessProfile',
            'allFrames',
            'unlockedFrameIds',
            'itineraries',
            'pointHistory',
            'pointTxTotal',
            'panoramaServiceRequests',
            'hasPendingPanoRequest',
            'notifications'
        ));
    }

    /** Hiển thị trang đăng ký nâng cấp lên tài khoản doanh nghiệp. */
    public function showBusinessUpgradeForm()
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();

        if ($businessProfile && in_array($businessProfile->status, ['pending', 'approved'])) {
            // Cho phép đăng ký lại nếu đã approved nhưng địa điểm trên bản đồ đã bị xóa.
            if ($businessProfile->status === 'approved') {
                $hasLocation = Location::where('created_by', $user->id)->exists();
                if (!$hasLocation) {
                    $categories = Category::where('status', 'active')->get();
                    return view('client.business_upgrade', compact('user', 'categories', 'businessProfile'));
                }
            }

            return redirect()->route('client.profile')->with('info', 'Bạn đã gửi yêu cầu nâng cấp hoặc đã có tài khoản doanh nghiệp.');
        }

        $categories = Category::where('status', 'active')->get();
        return view('client.business_upgrade', compact('user', 'categories', 'businessProfile'));
    }

    /**
     * Gợi ý địa điểm published chưa có chủ DN theo tên (autocomplete bước đăng ký).
     */
    public function suggestClaimableLocations(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $pendingClaimIds = BusinessProfile::where('status', 'pending')
            ->whereNotNull('location_id')
            ->pluck('location_id')
            ->all();

        $locations = Location::with(['category', 'images'])
            ->where('status', 'published')
            ->where('name', 'like', '%' . $q . '%')
            ->when(!empty($pendingClaimIds), fn ($query) => $query->whereNotIn('id', $pendingClaimIds))
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->filter(fn (Location $loc) => BusinessProfile::isLocationClaimable($loc))
            ->take(8)
            ->values()
            ->map(function (Location $loc) {
                $thumb = $loc->resolveThumbnailUrl();
                $gallery = collect($loc->resolveImageUrls())->pluck('url')->filter()->take(2)->values()->all();
                if (empty($gallery) && $thumb) {
                    $gallery = [$thumb];
                }
                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'address' => $loc->address ?: trim(implode(', ', array_filter([$loc->district, $loc->province]))),
                    'category' => $loc->category->name ?? null,
                    'category_id' => $loc->category_id,
                    'lat' => (float) $loc->lat,
                    'lng' => (float) $loc->lng,
                    'thumbnail' => $thumb,
                    'images' => $gallery,
                    'rating' => $loc->average_rating !== null ? (float) $loc->average_rating : null,
                    'review_count' => (int) ($loc->review_count ?? 0),
                    'description' => $loc->short_description ?: Str::limit((string) $loc->description, 150),
                ];
            });

        return response()->json(['data' => $locations]);
    }

    /** Cập nhật thông tin cá nhân cơ bản (tên hiển thị). */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'display_name' => 'required|string|max:120',
        ], [
            'display_name.required' => 'Vui lòng nhập tên hiển thị.',
        ]);

        $user->update([
            'display_name' => $validated['display_name'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tên hiển thị thành công!',
                'display_name' => $user->display_name
            ]);
        }

        return back()->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    /** Đổi mật khẩu (tài khoản Google không đổi được; tài khoản thường cần nhập mật khẩu hiện tại). */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->provider === 'google') {
            return back()->withErrors([
                'password' => 'Tài khoản đăng nhập bằng Google không thể đổi mật khẩu trên hệ thống.',
            ]);
        }

        // Người đăng nhập qua Google có thể chưa từng đặt mật khẩu
        $hasPassword = !empty($user->password_hash);
        
        $rules = [
            'password' => 'required|string|min:6|confirmed',
        ];

        // Chỉ bắt nhập mật khẩu hiện tại khi tài khoản đã có mật khẩu và không phải chỉ dùng OAuth
        if ($hasPassword && !$user->provider) {
            $rules['current_password'] = 'required|string';
        }

        $request->validate($rules, [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        if ($hasPassword && !$user->provider && !Hash::check($request->current_password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không chính xác.'],
            ]);
        }

        $user->update([
            'password_hash' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Thay đổi mật khẩu thành công!');
    }

    /** Cập nhật ảnh đại diện: lưu file mới, xóa file cũ (nếu là ảnh nội bộ). */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp,jfif',
                'mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,image/jfif,image/pjpeg',
                'max:5120',
            ],
        ], [
            'avatar.required' => 'Vui lòng chọn một hình ảnh.',
            'avatar.image' => 'Tệp được chọn phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, jpg, png, gif, svg, webp hoặc jfif.',
            'avatar.mimetypes' => 'Định dạng ảnh này chưa được hỗ trợ. Hãy thử ảnh jpg, png hoặc webp.',
            'avatar.max' => 'Kích thước hình ảnh tối đa là 5MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Lưu file avatar mới vào storage/app/public/avatars
            $avatarFile = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatarFile->getClientOriginalExtension();
            $path = $avatarFile->storeAs('avatars', $filename, 'public');

            // Xóa file avatar cũ nếu là ảnh lưu nội bộ
            if ($user->avatar_url && str_contains($user->avatar_url, 'avatars/') && !str_starts_with($user->avatar_url, 'http')) {
                $oldFilename = basename($user->avatar_url);
                Storage::disk('public')->delete('avatars/' . $oldFilename);
            }

            // Lưu đường dẫn avatar mới vào DB
            $avatarUrl = 'avatars/' . $filename;
            $user->update([
                'avatar_url' => $avatarUrl,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tải ảnh đại diện lên thành công!',
                'avatar_url' => $user->avatar_formatted_url,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể tải ảnh đại diện lên.',
        ], 400);
    }

    /** Thêm/bỏ yêu thích địa điểm qua AJAX; lần thêm mới sẽ cộng điểm và tính nhiệm vụ. */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $locationId = $request->location_id;

        $result = $user->favorites()->toggle($locationId);
        $isFavorited = count($result['attached']) > 0;

        $points = $user->points;
        if ($isFavorited) {
            $location = \App\Models\Location::find($locationId);
            \App\Services\PointService::awardPoints(
                $user,
                \App\Services\PointService::POINTS_FAVORITE,
                'favorite',
                'Yêu thích địa điểm ' . ($location->name ?? ('#' . $locationId))
            );
            MissionService::trackProgress($user, 'favorite_location', 1, false, $locationId);
            $points = $user->fresh()->points;
        }

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'points' => $points,
            'message' => $isFavorited
                ? 'Đã thêm vào danh sách yêu thích (+' . \App\Services\PointService::POINTS_FAVORITE . ' điểm)!'
                : 'Đã xóa khỏi danh sách yêu thích!',
        ]);
    }

    /** Xóa bình luận do chính người dùng tạo. */
    public function destroyComment(Comment $comment)
    {
        // Kiểm tra quyền sở hữu
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bình luận này.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bình luận thành công!',
        ]);
    }

    /** Vô hiệu hóa/xóa tài khoản: xác minh mật khẩu (hoặc username/email với tài khoản OAuth) rồi khóa. */
    public function deleteAccount(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Tài khoản thường: xác minh mật khẩu trước
        if (!$user->provider) {
            $request->validate([
                'confirm_password' => 'required|string',
            ], [
                'confirm_password.required' => 'Vui lòng nhập mật khẩu xác nhận.',
            ]);

            if (!Hash::check($request->confirm_password, $user->password_hash)) {
                throw ValidationException::withMessages([
                    'confirm_password' => ['Mật khẩu xác nhận không chính xác.'],
                ]);
            }
        } else {
            // Tài khoản OAuth: xác nhận bằng username/email thay cho mật khẩu
            $request->validate([
                'confirm_username' => 'required|string',
            ], [
                'confirm_username.required' => 'Vui lòng nhập tên tài khoản hoặc email để xác nhận.',
            ]);

            if ($request->confirm_username !== $user->email && $request->confirm_username !== $user->username) {
                throw ValidationException::withMessages([
                    'confirm_username' => ['Thông tin xác nhận không trùng khớp với tên tài khoản hoặc email của bạn.'],
                ]);
            }
        }

        // Đặt trạng thái 'deleted' để không thể đăng nhập nữa
        $user->update([
            'status' => 'deleted',
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Tài khoản của bạn đã bị vô hiệu hóa thành công.',
            'redirect_url' => route('home'),
        ]);
    }

    /** Xử lý tải ảnh doanh nghiệp trong lúc đăng ký (nén và lưu ở độ nét Full HD). */
    public function uploadBusinessPhoto(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ], [
            'file.required' => 'Vui lòng chọn hình ảnh.',
            'file.image' => 'Định dạng phải là hình ảnh.',
            'file.max' => 'Kích thước tối đa là 10MB.',
        ]);

        try {
            // Ảnh thư viện & ảnh đại diện doanh nghiệp là ảnh công khai: giữ nét ở mức Full HD
            $path = $this->compressAndSaveImage($request->file('file'), 'business/photos', 1920, 85);
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải ảnh lên: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý đăng ký/nộp lại hồ sơ doanh nghiệp.
     * Chặn gửi khi đang chờ duyệt hoặc đã có doanh nghiệp còn địa điểm; cho nộp lại khi
     * hồ sơ đã duyệt nhưng địa điểm đã bị gỡ hoặc hồ sơ đã bị từ chối.
     */
    public function businessRegister(Request $request)
    {
        $user = Auth::user();

        // Kiểm tra người dùng đã có hồ sơ doanh nghiệp đang hoạt động/chờ duyệt chưa
        $existing = BusinessProfile::where('user_id', $user->id)->first();
        if ($existing) {
            $blocked = false;
            $message = '';

            if ($existing->status === 'pending') {
                // Đang chờ duyệt -> không cho gửi lại
                $blocked = true;
                $message = 'Yêu cầu của bạn đang chờ duyệt. Vui lòng chờ quản trị viên xử lý.';
            } elseif ($existing->status === 'approved') {
                // Chỉ chặn nếu địa điểm vẫn còn trên bản đồ.
                // Nếu địa điểm đã bị xóa thì cho phép đăng ký lại (khớp logic showBusinessUpgradeForm).
                $hasLocation = Location::where('created_by', $user->id)->exists();
                if ($hasLocation) {
                    $blocked = true;
                    $message = 'Bạn đã có tài khoản doanh nghiệp đang hoạt động.';
                }
            }

            if ($blocked) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'redirect' => route('client.profile'),
                ], 400);
            }
        }

        $isClaim = $request->filled('location_id');

        $rules = [
            'business_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'business_documents' => 'nullable|array',
            'verification_photo' => 'nullable|string',
            'verification_photos' => 'required|array|min:1',
            'verification_lat' => 'required|numeric',
            'verification_lng' => 'required|numeric',
            'verification_time' => 'nullable|string',
            'location_id' => 'nullable|integer|exists:locations,id',
            'website' => 'nullable|string|max:255',
            'receive_tips' => 'nullable|boolean',
            'receive_surveys' => 'nullable|boolean',
        ];

        if ($isClaim) {
            $rules += [
                'business_types' => 'nullable|array',
                'category_id' => 'nullable|exists:categories,id',
                'address_country' => 'nullable|string|max:100',
                'address_street' => 'nullable|string|max:255',
                'address_city' => 'nullable|string|max:255',
                'address_province' => 'nullable|string|max:255',
                'address_postal_code' => 'nullable|string|max:20',
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'description' => 'nullable|string|max:750',
                'menu_photos' => 'nullable|array',
                'storefront_photos' => 'nullable|array',
                'avatar_photo' => 'nullable|string',
            ];
        } else {
            $rules += [
                'business_types' => 'nullable|array',
                'category_id' => 'required|exists:categories,id',
                'address_country' => 'required|string|max:100',
                'address_street' => 'required|string|max:255',
                'address_city' => 'required|string|max:255',
                'address_province' => 'required|string|max:255',
                'address_postal_code' => 'required|string|max:20',
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
                'description' => 'nullable|string|max:750',
                'menu_photos' => 'nullable|array',
                'storefront_photos' => 'nullable|array',
                'avatar_photo' => 'nullable|string',
            ];
        }

            $validated = $request->validate($rules, [
            'business_name.required' => 'Vui lòng nhập tên doanh nghiệp.',
            'category_id.required' => 'Vui lòng chọn danh mục kinh doanh.',
            'address_street.required' => 'Vui lòng nhập địa chỉ đường phố.',
            'address_city.required' => 'Vui lòng nhập thành phố.',
            'address_province.required' => 'Vui lòng nhập tỉnh/bang.',
            'address_postal_code.required' => 'Vui lòng nhập mã bưu chính.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'lat.required' => 'Vui lòng chọn tọa độ bản đồ.',
            'lng.required' => 'Vui lòng chọn tọa độ bản đồ.',
            'verification_photos.required' => 'Vui lòng chụp ảnh xác thực thực địa.',
            'verification_photos.min' => 'Vui lòng chụp ít nhất 1 ảnh xác thực thực địa.',
            'verification_lat.required' => 'Chưa lấy được tọa độ GPS. Vui lòng bật Vị trí rồi nhấn Lấy lại GPS.',
            'verification_lng.required' => 'Chưa lấy được tọa độ GPS. Vui lòng bật Vị trí rồi nhấn Lấy lại GPS.',
            'location_id.exists' => 'Địa điểm được chọn không tồn tại.',
        ]);

        $claimLocation = null;
        if ($isClaim) {
            $claimLocation = Location::with('category')->find($validated['location_id']);
            if (!BusinessProfile::isLocationClaimable($claimLocation)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Địa điểm này không còn nhận được (đã có chủ doanh nghiệp hoặc không công khai).',
                ], 422);
            }

            $alreadyPending = BusinessProfile::where('location_id', $claimLocation->id)
                ->where('status', 'pending')
                ->where('user_id', '!=', $user->id)
                ->exists();
            if ($alreadyPending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Địa điểm này đang có yêu cầu nhận quyền chờ duyệt từ tài khoản khác.',
                ], 422);
            }

            // Prefill hồ sơ từ POI đã chọn
            $validated['business_name'] = $validated['business_name'] ?: $claimLocation->name;
            $validated['category_id'] = $claimLocation->category_id;
            $validated['business_types'] = !empty($validated['business_types'])
                ? $validated['business_types']
                : ['local_store'];
            $validated['address_country'] = 'Việt Nam';
            $validated['address_province'] = $claimLocation->province ?: 'Ninh Bình';
            $validated['address_city'] = $claimLocation->district ?: ($claimLocation->ward ?: 'Ninh Bình');
            $validated['address_street'] = $claimLocation->address ?: $claimLocation->name;
            $validated['address_postal_code'] = $validated['address_postal_code'] ?? '00000';
            $validated['lat'] = (float) $claimLocation->lat;
            $validated['lng'] = (float) $claimLocation->lng;
            $validated['description'] = $validated['description']
                ?? ($claimLocation->short_description ?: Str::limit((string) $claimLocation->description, 750));
            $validated['menu_photos'] = $validated['menu_photos'] ?? [];
            $validated['storefront_photos'] = $validated['storefront_photos'] ?? [];
            $validated['avatar_photo'] = $validated['avatar_photo'] ?? $claimLocation->thumbnail_url;
        }

        // Chặn gửi nếu GPS xác thực cách vị trí ghim > 500m
        $pinLat = isset($validated['lat']) ? (float) $validated['lat'] : null;
        $pinLng = isset($validated['lng']) ? (float) $validated['lng'] : null;
        $vLat = (float) $validated['verification_lat'];
        $vLng = (float) $validated['verification_lng'];
        if ($pinLat !== null && $pinLng !== null && is_finite($pinLat) && is_finite($pinLng)) {
            $maxDistanceM = 500;
            $distanceM = $this->haversineMeters($vLat, $vLng, $pinLat, $pinLng);
            if ($distanceM > $maxDistanceM) {
                $shown = $distanceM >= 1000
                    ? round($distanceM / 1000, 1) . 'km'
                    : (string) (int) round($distanceM) . 'm';
                return response()->json([
                    'success' => false,
                    'message' => "Bạn đang cách {$shown} vị trí được ghim; chỉ có thể xác thực trong khoảng {$maxDistanceM}m.",
                ], 422);
            }
        }

        $savedVerificationPhotos = [];
        $rawVerificationPhotos = $validated['verification_photos'] ?? [];
        if (!empty($validated['verification_photo']) && empty($rawVerificationPhotos)) {
            $rawVerificationPhotos = [$validated['verification_photo']];
        }

        foreach ((array)$rawVerificationPhotos as $idx => $photoVal) {
            if (empty($photoVal)) continue;
            if (str_starts_with($photoVal, 'data:image/')) {
                // Ảnh dạng base64 chụp từ camera (canvas) -> giải mã và lưu thành file
                @list($type, $data) = explode(';', $photoVal);
                @list(, $data)      = explode(',', $data);
                if ($data) {
                    $imageData = base64_decode($data);
                    $filename = 'business/verification/verify_' . $user->id . '_' . time() . '_' . $idx . '_' . \Illuminate\Support\Str::random(6) . '.jpg';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);
                    $savedVerificationPhotos[] = $filename;
                }
            } else {
                $savedVerificationPhotos[] = $photoVal;
            }
        }

        $verificationPhotoPath = $savedVerificationPhotos[0] ?? null;
        $verificationTime = !empty($validated['verification_time']) ? \Carbon\Carbon::parse($validated['verification_time']) : now();

        $profilePayload = [
            'business_name' => $validated['business_name'],
            'business_types' => !empty($validated['business_types']) ? $validated['business_types'] : ['local_store'],
            'category_id' => $validated['category_id'],
            'address_country' => $validated['address_country'] ?? 'Việt Nam',
            'address_street' => $validated['address_street'],
            'address_city' => $validated['address_city'],
            'address_province' => $validated['address_province'],
            'address_postal_code' => $validated['address_postal_code'] ?? '00000',
            'phone' => $validated['phone'],
            'website' => $validated['website'] ?? null,
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'receive_tips' => (bool)($validated['receive_tips'] ?? false),
            'receive_surveys' => (bool)($validated['receive_surveys'] ?? false),
            'description' => $validated['description'] ?? null,
            'menu_photos' => $validated['menu_photos'] ?? [],
            'storefront_photos' => $validated['storefront_photos'] ?? [],
            'avatar_photo' => $validated['avatar_photo'] ?? null,
            'business_documents' => $validated['business_documents'] ?? [],
            'verification_photo' => $verificationPhotoPath,
            'verification_photos' => $savedVerificationPhotos,
            'verification_lat' => $validated['verification_lat'] ?? null,
            'verification_lng' => $validated['verification_lng'] ?? null,
            'verification_time' => $verificationTime,
            'location_id' => $claimLocation?->id,
            'status' => 'pending',
            'reject_reason' => null,
        ];

        if ($existing) {
            // Cập nhật lại hồ sơ cũ (đã bị từ chối hoặc địa điểm đã bị gỡ) và chuyển về trạng thái chờ duyệt
            $existing->update($profilePayload);
            $business = $existing;
        } else {
            // Tạo hồ sơ doanh nghiệp mới
            $business = BusinessProfile::create(array_merge($profilePayload, [
                'user_id' => $user->id,
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => $isClaim
                ? 'Đã gửi yêu cầu nhận quyền địa điểm trên bản đồ. Vui lòng chờ quản trị viên duyệt.'
                : 'Đăng ký tài khoản doanh nghiệp thành công! Yêu cầu đang được chờ phê duyệt.',
            'business' => $business
        ]);
    }

    /** Hủy yêu cầu đăng ký doanh nghiệp đang chờ duyệt và dọn các ảnh đã tải lên. */
    public function cancelBusinessRegistration()
    {
        $user = Auth::user();
        
        $profile = BusinessProfile::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu nâng cấp nào đang ở trạng thái chờ duyệt.'
            ], 404);
        }

        // Dọn các file ảnh đã tải lên (nếu còn tồn tại)
        $photos = array_merge($profile->menu_photos ?? [], $profile->storefront_photos ?? []);
        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo)) {
                Storage::disk('public')->delete($photo);
            }
        }

        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy yêu cầu đăng ký doanh nghiệp thành công.'
        ]);
    }

    /** Nén ảnh bằng GD và lưu dưới dạng WebP (giữ nền trong suốt cho PNG/WebP). */
    private function compressAndSaveImage($file, $folder, $maxWidth = 1200, $quality = 75)
    {
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return $file->store($folder, 'public');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = @imagecreatefromjpeg($file->getRealPath());
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($file->getRealPath());
                break;
            case 'image/webp':
                $sourceImage = @imagecreatefromwebp($file->getRealPath());
                break;
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($file->getRealPath());
                break;
            default:
                $sourceImage = null;
        }

        if (!$sourceImage) {
            return $file->store($folder, 'public');
        }

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)(($height / $width) * $newWidth);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime == 'image/png' || $mime == 'image/webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $filename = Str::random(40) . '.webp';
        $relativeStoragePath = $folder . '/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativeStoragePath);

        $dir = dirname($absolutePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        imagewebp($targetImage, $absolutePath, $quality);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $relativeStoragePath;
    }

    /** Ghi nhận thời gian online cho nhiệm vụ active_session (không cộng xu theo từng phút). */
    public function heartbeat(Request $request)
    {
        $user = Auth::user();

        $sessionMission = \App\Models\Mission::where('status', 'active')
            ->where('action_key', 'active_session')
            ->first();

        $target = $sessionMission ? (int) $sessionMission->target_count : 15;

        MissionService::trackProgress($user, 'active_session', 1);

        $userMission = null;
        if ($sessionMission) {
            $userMission = \App\Models\UserMission::where('user_id', $user->id)
                ->where('mission_id', $sessionMission->id)
                ->first();
        }

        $minutes = $userMission ? (int) $userMission->current_count : 0;
        $capped = $minutes >= $target;

        return response()->json([
            'success' => !$capped || ($userMission && $userMission->status === 'in_progress'),
            'minutes' => min($minutes, $target),
            'target' => $target,
            'claimable' => $userMission && $userMission->status === 'completed',
            'points' => $user->fresh()->points,
            'message' => $capped
                ? ($userMission && $userMission->status === 'completed'
                    ? 'Đã đủ thời gian — hãy nhận thưởng nhiệm vụ.'
                    : 'Đã đủ thời gian online cho nhiệm vụ hôm nay.')
                : 'Đã ghi nhận thời gian online (' . $minutes . '/' . $target . ' phút).',
        ]);
    }

    /** Nhận điểm điểm danh hằng ngày (kèm thưởng theo chuỗi ngày). */
    public function claimDaily(Request $request)
    {
        $user = Auth::user();
        $result = MissionService::processDailyCheckin($user);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'points' => $result['points'],
                'streak' => $result['streak'],
                'message' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /** Trang Trung tâm nhiệm vụ & phần thưởng (nhiệm vụ, khung avatar, bảng xếp hạng, cửa hàng). */
    public function missions()
    {
        $user = Auth::user();

        if ($user) {
            // Ghi nhận lượt đăng nhập trong ngày
            MissionService::trackProgress($user, 'daily_login', 1);

            // Lấy tiến độ nhiệm vụ của người dùng
            $userMissions = UserMission::where('user_id', $user->id)
                ->get()
                ->keyBy('mission_id');

            $unlockedFrameIds = UserAvatarFrame::where('user_id', $user->id)
                ->pluck('avatar_frame_id')
                ->toArray();
        } else {
            $userMissions = collect();
            $unlockedFrameIds = [];
        }

        // Lấy các nhiệm vụ đang hoạt động và phân theo loại
        $allMissions = Mission::where('status', 'active')->with('rewardFrame')->get();

        $dailyMissions = $allMissions->where('type', 'daily');
        $weeklyMissions = $allMissions->where('type', 'weekly');
        $achievementMissions = $allMissions->where('type', 'achievement');

        // Lấy danh sách khung avatar
        $allFrames = AvatarFrame::where('status', 'active')->orderBy('required_points', 'asc')->get();

        // Bảng xếp hạng (Top 5 người dùng nhiều điểm nhất)
        $leaderboard = User::orderBy('points', 'desc')->take(5)->get();

        $milestoneFrameCodes = ['frame-bronze', 'frame-silver', 'frame-diamond', 'frame-streak'];
        $shopFrames = AvatarFrame::where('status', 'active')
            ->whereNotIn('code', $milestoneFrameCodes)
            ->where('required_points', '>', 0)
            ->whereNotIn('id', $unlockedFrameIds)
            ->orderBy('required_points')
            ->get();
        $shopRewards = Reward::active()->orderBy('cost_points')->get();
        $userRedemptions = $user
            ? UserRedemption::where('user_id', $user->id)->with('reward')->latest()->get()
            : collect();
        $redeemedRewardIds = $userRedemptions->pluck('reward_id')->all();

        return view('client.missions.index', compact(
            'user',
            'dailyMissions',
            'weeklyMissions',
            'achievementMissions',
            'userMissions',
            'allFrames',
            'unlockedFrameIds',
            'leaderboard',
            'shopFrames',
            'shopRewards',
            'userRedemptions',
            'redeemedRewardIds'
        ));
    }

    /** Đổi một phần thưởng trong cửa hàng bằng xu (ủy quyền cho RewardService). */
    public function redeemReward(Reward $reward, RewardService $rewardService)
    {
        $result = $rewardService->redeem(Auth::user(), $reward);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /** Nhận thưởng cho một nhiệm vụ đã hoàn thành. */
    public function claimMissionReward(Request $request, int $missionId)
    {
        $user = Auth::user();
        $result = MissionService::claimReward($user, $missionId);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /** Nhận quà theo mốc tích lũy xu: chỉ mở khóa khung avatar, không cộng thêm xu (tránh lạm phát điểm). */
    public function claimMilestone100(Request $request)
    {
        $user = Auth::user();

        $totalPointsEarned = \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->where('action', 'not like', 'daily_milestone_%')
            ->sum('amount');

        $claimedMilestones = \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('action', 'like', 'daily_milestone_%')
            ->pluck('action')
            ->toArray();

        $allowedMilestones = [100, 200, 500];
        $reqMilestone = (int) $request->input('milestone', 100);

        if (!in_array($reqMilestone, $allowedMilestones, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Mốc phần thưởng không hợp lệ!',
            ], 400);
        }

        if ($totalPointsEarned < $reqMilestone) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần tích lũy ít nhất ' . $reqMilestone . ' xu để mở Hộp Quà Mốc này.',
            ], 400);
        }

        $actionKey = 'daily_milestone_' . $reqMilestone;
        if (in_array($actionKey, $claimedMilestones, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nhận phần thưởng mốc ' . $reqMilestone . ' xu rồi!',
            ], 400);
        }

        // Tra khung theo mã code (ổn định) thay vì ID cứng
        $frameCodes = [
            100 => 'frame-bronze',
            200 => 'frame-silver',
            500 => 'frame-diamond',
        ];

        $frame = \App\Models\AvatarFrame::where('code', $frameCodes[$reqMilestone])->first();

        \App\Services\PointService::awardPoints(
            $user,
            0,
            $actionKey,
            'Nhận khung mốc tích lũy ' . $reqMilestone . ' xu'
        );

        if ($frame) {
            MissionService::unlockFrame($user, $frame->id);
        }

        return response()->json([
            'success' => true,
            'message' => $frame
                ? 'Đã mở khóa khung avatar: ' . $frame->name . '!'
                : 'Đã nhận phần thưởng mốc ' . $reqMilestone . ' xu!',
            'coins' => 0,
            'points' => $user->fresh()->points,
            'frame' => $frame ? [
                'id' => $frame->id,
                'name' => $frame->name,
                'image_url' => $frame->image_url ? asset($frame->image_url) : '',
                'css_style' => $frame->css_style,
            ] : null,
        ]);
    }

    /** Trang bị hoặc tháo khung avatar. */
    public function equipAvatarFrame(Request $request)
    {
        $user = Auth::user();
        $frameId = $request->input('frame_id');
        $result = MissionService::equipFrame($user, $frameId ? (int)$frameId : null);

        return response()->json($result);
    }

    /** (Đã vô hiệu hóa) Mua khung avatar bằng xu — khung chỉ là phần thưởng thành tích. */
    public function buyAvatarFrame(Request $request, int $frameId)
    {
        $user = Auth::user();
        $result = MissionService::purchaseFrame($user, $frameId);

        return response()->json($result);
    }

    /** Khoảng cách haversine giữa 2 tọa độ (mét). */
    private function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }
}

