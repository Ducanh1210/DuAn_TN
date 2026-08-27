<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Controller quản trị người dùng: liệt kê, thêm, sửa, xóa, khóa/mở khóa.
 */
class UserController extends Controller
{
    /** Danh sách người dùng (ẩn tài khoản admin). */
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /** Form tạo người dùng mới. */
    public function create()
    {
        return view('admin.users.create');
    }

    /** Lưu người dùng mới vào cơ sở dữ liệu. */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = new User();
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->display_name = $validated['display_name'];
        $user->password_hash = md5($validated['password']);
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        $user->provider = 'local';

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = 'avatars/' . basename($path);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được tạo thành công.');
    }

    /** Trang chi tiết người dùng. */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /** Form sửa người dùng. */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /** Cập nhật người dùng (đổi mật khẩu nếu có nhập, thay avatar nếu có tải lên). */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validated();

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->display_name = $validated['display_name'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password_hash = md5($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && str_contains($user->avatar_url, 'avatars/') && !str_starts_with($user->avatar_url, 'http')) {
                Storage::disk('public')->delete('avatars/' . basename($user->avatar_url));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = 'avatars/' . basename($path);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    /** Xóa người dùng (không cho tự xóa chính mình). */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được xóa thành công.');
    }

    /** Khóa/mở khóa tài khoản người dùng (không cho tự khóa chính mình). */
    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể khóa tài khoản của chính mình.');
        }

        $user->status = $user->status === 'banned' ? 'active' : 'banned';
        $user->save();

        $action = $user->status === 'banned' ? 'Khóa' : 'Mở khóa';
        return redirect()->route('admin.users.index')->with('success', "{$action} tài khoản thành công.");
    }
}
