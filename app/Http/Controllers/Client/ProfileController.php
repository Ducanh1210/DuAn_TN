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
use App\Services\MissionService;
use Illuminate\Support\Str;


class ProfileController extends Controller
{
    /**
     * Display the user's profile and settings dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load favorite locations with category and images
        $favorites = $user->favorites()
            ->with(['category', 'images'])
            ->get();
            
        // Resolve thumbnail URLs for favorite locations
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

        // Load comments with locations
        $comments = $user->comments()
            ->with('location')
            ->get();

        // Load categories and business profile
        $categories = Category::where('status', 'active')->get();
        $businessProfile = BusinessProfile::where('user_id', $user->id)->with('category')->first();

        // Load Avatar Frames
        $allFrames = AvatarFrame::where('status', 'active')->orderBy('required_points', 'asc')->get();
        $unlockedFrameIds = UserAvatarFrame::where('user_id', $user->id)
            ->pluck('avatar_frame_id')
            ->toArray();

        return view('client.profile', compact('user', 'favorites', 'comments', 'categories', 'businessProfile', 'allFrames', 'unlockedFrameIds'));
    }

    /**
     * Show the dedicated business account upgrade page.
     */
    public function showBusinessUpgradeForm()
    {
        $user = Auth::user();
        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();

        if ($businessProfile && in_array($businessProfile->status, ['pending', 'approved'])) {
            return redirect()->route('client.profile')->with('info', 'Bạn đã gửi yêu cầu nâng cấp hoặc đã có tài khoản doanh nghiệp.');
        }

        $categories = Category::where('status', 'active')->get();
        return view('client.business_upgrade', compact('user', 'categories', 'businessProfile'));
    }

    /**
     * Update the user's general profile information.
     */
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

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user registered via Google and hasn't set a password yet
        $hasPassword = !empty($user->password_hash);
        
        $rules = [
            'password' => 'required|string|min:6|confirmed',
        ];

        // Only require current password if they have a password and are not OAuth-only
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

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn một hình ảnh.',
            'avatar.image' => 'Tệp được chọn phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, hoặc svg.',
            'avatar.max' => 'Kích thước hình ảnh tối đa là 2MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Upload new avatar file to storage/app/public/avatars
            $avatarFile = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatarFile->getClientOriginalExtension();
            $path = $avatarFile->storeAs('avatars', $filename, 'public');

            // Delete old local avatar file if exists
            if ($user->avatar_url && str_contains($user->avatar_url, 'avatars/') && !str_starts_with($user->avatar_url, 'http')) {
                $oldFilename = basename($user->avatar_url);
                Storage::disk('public')->delete('avatars/' . $oldFilename);
            }

            // Update database avatar_url to path accessible via storage route
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

    /**
     * Toggle favorite status of a location via AJAX.
     */
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

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Đã thêm vào danh sách yêu thích!' : 'Đã xóa khỏi danh sách yêu thích!',
        ]);
    }

    /**
     * Delete a comment created by the user.
     */
    public function destroyComment(Comment $comment)
    {
        // Check ownership
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

    /**
     * Deactivate/delete user account.
     */
    public function deleteAccount(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If not OAuth user, verify password first
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
            // OAuth user needs to check username/email to confirm
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

        // Set status to locked or deleted so they cannot log in
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

    /**
     * Handle business photo uploads during registration.
     */
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
            $path = $this->compressAndSaveImage($request->file('file'), 'business/photos');
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
     * Handle business account registration.
     */
    public function businessRegister(Request $request)
    {
        $user = Auth::user();

        // Check if user already has an active or pending business profile
        $existing = BusinessProfile::where('user_id', $user->id)->first();
        if ($existing && in_array($existing->status, ['pending', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã gửi yêu cầu nâng cấp hoặc đã có tài khoản doanh nghiệp.'
            ], 400);
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_types' => 'required|array|min:1',
            'category_id' => 'required|exists:categories,id',
            'address_country' => 'required|string|max:100',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_province' => 'required|string|max:255',
            'address_postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:30',
            'website' => 'nullable|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'receive_tips' => 'nullable|boolean',
            'receive_surveys' => 'nullable|boolean',
            'description' => 'nullable|string|max:750',
            'menu_photos' => 'nullable|array',
            'storefront_photos' => 'nullable|array',
        ], [
            'business_name.required' => 'Vui lòng nhập tên doanh nghiệp.',
            'business_types.required' => 'Vui lòng chọn loại hình doanh nghiệp.',
            'category_id.required' => 'Vui lòng chọn danh mục kinh doanh.',
            'address_street.required' => 'Vui lòng nhập địa chỉ đường phố.',
            'address_city.required' => 'Vui lòng nhập thành phố.',
            'address_province.required' => 'Vui lòng nhập tỉnh/bang.',
            'address_postal_code.required' => 'Vui lòng nhập mã bưu chính.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'lat.required' => 'Vui lòng chọn tọa độ bản đồ.',
            'lng.required' => 'Vui lòng chọn tọa độ bản đồ.',
        ]);

        if ($existing) {
            // Update the existing rejected one
            $existing->update([
                'business_name' => $validated['business_name'],
                'business_types' => $validated['business_types'],
                'category_id' => $validated['category_id'],
                'address_country' => $validated['address_country'],
                'address_street' => $validated['address_street'],
                'address_city' => $validated['address_city'],
                'address_province' => $validated['address_province'],
                'address_postal_code' => $validated['address_postal_code'],
                'phone' => $validated['phone'],
                'website' => $validated['website'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'receive_tips' => (bool)($validated['receive_tips'] ?? false),
                'receive_surveys' => (bool)($validated['receive_surveys'] ?? false),
                'description' => $validated['description'] ?? null,
                'menu_photos' => $validated['menu_photos'] ?? [],
                'storefront_photos' => $validated['storefront_photos'] ?? [],
                'status' => 'pending',
                'reject_reason' => null,
            ]);
            $business = $existing;
        } else {
            // Create a new business profile
            $business = BusinessProfile::create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'business_types' => $validated['business_types'],
                'category_id' => $validated['category_id'],
                'address_country' => $validated['address_country'],
                'address_street' => $validated['address_street'],
                'address_city' => $validated['address_city'],
                'address_province' => $validated['address_province'],
                'address_postal_code' => $validated['address_postal_code'],
                'phone' => $validated['phone'],
                'website' => $validated['website'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'receive_tips' => (bool)($validated['receive_tips'] ?? false),
                'receive_surveys' => (bool)($validated['receive_surveys'] ?? false),
                'description' => $validated['description'] ?? null,
                'menu_photos' => $validated['menu_photos'] ?? [],
                'storefront_photos' => $validated['storefront_photos'] ?? [],
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản doanh nghiệp thành công! Yêu cầu đang được chờ phê duyệt.',
            'business' => $business
        ]);
    }

    /**
     * Cancel a pending business profile registration.
     */
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

        // Clean up uploaded files in local storage if they exist
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

    /**
     * Compress image using GD and save as WebP.
     */
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

    /**
     * Track user activity and award points for time spent on site.
     */
    public function heartbeat(Request $request)
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today();

        // Count points gained from active session today
        $todayPoints = \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('action', 'active_session')
            ->whereDate('created_at', $today)
            ->sum('amount');

        // Cap active session points to 60 per day (1 point per minute for 1 hour)
        if ($todayPoints < 60) {
            \App\Services\PointService::awardPoints($user, 1, 'active_session', 'Tích lũy thời gian hoạt động');
            return response()->json([
                'success' => true,
                'points' => $user->points,
                'message' => 'Cộng +1 xu hoạt động.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Đã đạt giới hạn xu hoạt động hôm nay.'
        ]);
    }

    /**
     * Claim daily login bonus points with streak bonus.
     */
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

    /**
     * Display the Quests & Avatar Frame Reward Center.
     */
    public function missions()
    {
        $user = Auth::user();

        // Track daily login
        MissionService::trackProgress($user, 'daily_login', 1);

        // Fetch active missions
        $allMissions = Mission::where('status', 'active')->with('rewardFrame')->get();

        // Fetch user progress
        $userMissions = UserMission::where('user_id', $user->id)
            ->get()
            ->keyBy('mission_id');

        $dailyMissions = $allMissions->where('type', 'daily');
        $weeklyMissions = $allMissions->where('type', 'weekly');
        $achievementMissions = $allMissions->where('type', 'achievement');

        // Fetch Avatar Frames
        $allFrames = AvatarFrame::where('status', 'active')->orderBy('required_points', 'asc')->get();
        $unlockedFrameIds = UserAvatarFrame::where('user_id', $user->id)
            ->pluck('avatar_frame_id')
            ->toArray();

        // Fetch Leaderboard (Top 5 Users)
        $leaderboard = User::orderBy('points', 'desc')->take(5)->get();

        return view('client.missions.index', compact(
            'user',
            'dailyMissions',
            'weeklyMissions',
            'achievementMissions',
            'userMissions',
            'allFrames',
            'unlockedFrameIds',
            'leaderboard'
        ));
    }

    /**
     * Claim reward for a completed mission.
     */
    public function claimMissionReward(Request $request, int $missionId)
    {
        $user = Auth::user();
        $result = MissionService::claimReward($user, $missionId);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Claim daily 100-point milestone gift box reward.
     */
    public function claimMilestone100(Request $request)
    {
        $user = Auth::user();

        // 1. Exclude milestone rewards from earned points progress
        $totalPointsEarned = \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->where('action', 'not like', 'daily_milestone_%')
            ->sum('amount');

        $claimedMilestones = \App\Models\PointTransaction::where('user_id', $user->id)
            ->where('action', 'like', 'daily_milestone_%')
            ->pluck('action')
            ->toArray();

        $allowedMilestones = [100, 200, 500];
        $reqMilestone = (int)$request->input('milestone', 100);

        if (!in_array($reqMilestone, $allowedMilestones)) {
            return response()->json([
                'success' => false,
                'message' => 'Mốc phần thưởng không hợp lệ!'
            ], 400);
        }

        if ($totalPointsEarned < $reqMilestone) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần tích lũy ít nhất ' . $reqMilestone . ' xu để mở Hộp Quà Mốc này.'
            ], 400);
        }

        $actionKey = 'daily_milestone_' . $reqMilestone;
        if (in_array($actionKey, $claimedMilestones)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nhận phần thưởng mốc ' . $reqMilestone . ' xu rồi!'
            ], 400);
        }

        // Rewards map (Focus on avatar frames with small coin bonus)
        $rewardsMap = [
            100 => ['coins' => 10, 'frame_id' => 1],
            200 => ['coins' => 20, 'frame_id' => 2],
            500 => ['coins' => 30, 'frame_id' => 4],
        ];

        $rewardCoins = $rewardsMap[$reqMilestone]['coins'];
        $frameId = $rewardsMap[$reqMilestone]['frame_id'];

        \App\Services\PointService::awardPoints($user, $rewardCoins, $actionKey, 'Thưởng mốc tích lũy ' . $reqMilestone . ' xu');

        $frame = \App\Models\AvatarFrame::find($frameId);
        if ($frame) {
            \App\Services\MissionService::unlockFrame($user, $frame->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bạn nhận thêm +' . $rewardCoins . ' xu thưởng từ Hộp Quà Mốc ' . $reqMilestone . ' Xu!',
            'coins' => $rewardCoins,
            'points' => $user->fresh()->points,
            'frame' => $frame ? [
                'id' => $frame->id,
                'name' => $frame->name,
                'image_url' => asset($frame->image_url),
                'css_style' => $frame->css_style
            ] : null
        ]);
    }

    /**
     * Equip or unequip an avatar frame.
     */
    public function equipAvatarFrame(Request $request)
    {
        $user = Auth::user();
        $frameId = $request->input('frame_id');
        $result = MissionService::equipFrame($user, $frameId ? (int)$frameId : null);

        return response()->json($result);
    }

    /**
     * Buy an avatar frame with earned points.
     */
    public function buyAvatarFrame(Request $request, int $frameId)
    {
        $user = Auth::user();
        $result = MissionService::purchaseFrame($user, $frameId);

        return response()->json($result);
    }
}

