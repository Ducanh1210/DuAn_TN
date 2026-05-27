<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('display_order', 'asc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:80|unique:categories',
            'status' => 'required|in:active,hidden',
            'display_order' => 'nullable|integer',
            'icon_color' => 'nullable|string|max:20',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
        ]);

        $data = $request->except('icon');
        $data['slug'] = Str::slug($request->name);
        if (!$request->filled('display_order')) {
            $data['display_order'] = Category::max('display_order') + 1;
        }

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $data['icon'] = 'uploads/categories/' . $filename;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:80|unique:categories,name,' . $category->id,
            'status' => 'required|in:active,hidden',
            'display_order' => 'required|integer',
            'icon_color' => 'nullable|string|max:20',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
        ]);

        $data = $request->except('icon');
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('icon')) {
            if ($category->icon && file_exists(public_path($category->icon))) {
                @unlink(public_path($category->icon));
            }
            $file = $request->file('icon');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $data['icon'] = 'uploads/categories/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->locations()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục đang có chứa địa điểm!');
        }
        
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}
