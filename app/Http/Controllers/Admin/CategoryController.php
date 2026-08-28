<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use Illuminate\Support\Str;

/**
 * Controller quản trị danh mục địa điểm: liệt kê, thêm, sửa, xóa và xử lý upload icon.
 */
class CategoryController extends Controller
{
    /** Danh sách danh mục (sắp theo thứ tự hiển thị). */
    public function index()
    {
        $categories = Category::withCount('locations')->orderBy('display_order', 'asc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /** Form tạo danh mục mới. */
    public function create()
    {
        return view('admin.categories.create');
    }

    /** Lưu danh mục mới: tạo slug, tự tăng thứ tự hiển thị và lưu icon (nếu có). */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        $data = $request->except('icon');
        $data['slug'] = Str::slug($request->name);
        if (!$request->filled('display_order')) {
            $data['display_order'] = (int) Category::max('display_order') + 1;
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

    /** Form sửa danh mục. */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /** Cập nhật danh mục: cập nhật slug và thay icon (xóa icon cũ nếu có). */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

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

    /** Xóa danh mục (chặn nếu còn địa điểm thuộc danh mục này). */
    public function destroy(Category $category)
    {
        $locationsCount = $category->locations()->count();
        if ($locationsCount > 0) {
            return back()->with('error', 'Không thể xóa! Danh mục "' . $category->name . '" đang chứa ' . $locationsCount . ' địa điểm du lịch.');
        }
        
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}
