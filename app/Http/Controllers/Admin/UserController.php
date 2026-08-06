<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Services\PointService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Loại bỏ các tài khoản có vai trò 'admin' khỏi danh sách
        $users = User::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'display_name' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,moderator,user',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->display_name = $request->display_name;
        // In the model it mentions "custom MD5". Let's use md5 for now, but usually it's better to use Hash::make if possible.
        // Based on app/Models/User.php: // Disable default hashing casting since we use custom MD5
        $user->password_hash = md5($request->password);
        $user->role = $request->role;
        $user->status = $request->status;
        $user->provider = 'local';
        
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = 'avatars/' . basename($path);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        $historyData = PointService::aggregatedHistory($user->id);
        $perPage = 15;
        $page = (int) request('points_page', 1);
        $items = collect($historyData['history']);

        $pointHistory = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'points_page']
        );

        return view('admin.users.show', compact('user', 'pointHistory', 'historyData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:100|unique:users,email,'.$user->id,
            'display_name' => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,moderator,user',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->display_name = $request->display_name;
        $user->role = $request->role;
        $user->status = $request->status;
        
        if ($request->filled('password')) {
            $user->password_hash = md5($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar file if exists
            if ($user->avatar_url && str_contains($user->avatar_url, 'avatars/') && !str_starts_with($user->avatar_url, 'http')) {
                Storage::disk('public')->delete('avatars/' . basename($user->avatar_url));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = 'avatars/' . basename($path);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được xóa thành công.');
    }

    /**
     * Toggle user status (Lock/Unlock)
     */
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

    /**
     * Adjust user points (Award or subtract)
     */
    public function adjustPoints(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'amount' => 'required|integer',
            'description' => 'required|string|max:255',
        ]);

        $amount = (int) $request->input('amount');
        $description = $request->input('description');

        \App\Services\PointService::awardPoints($user, $amount, 'manual_adjust', $description);

        return redirect()->route('admin.users.show', $user->id)->with('success', 'Điểm số của người dùng đã được cập nhật.');
    }
}
