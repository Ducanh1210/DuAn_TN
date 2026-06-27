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

        return view('client.profile', compact('user', 'favorites', 'comments'));
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
            if ($user->avatar_url && str_contains($user->avatar_url, '/storage/avatars/')) {
                $oldFilename = basename($user->avatar_url);
                Storage::disk('public')->delete('avatars/' . $oldFilename);
            }

            // Update database avatar_url to path accessible via storage route
            $avatarUrl = '/storage/avatars/' . $filename;
            $user->update([
                'avatar_url' => $avatarUrl,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tải ảnh đại diện lên thành công!',
                'avatar_url' => $avatarUrl,
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
}
