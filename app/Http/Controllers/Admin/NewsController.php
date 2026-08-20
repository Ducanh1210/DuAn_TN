<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Controller quản trị tin tức/bài viết: liệt kê (tìm kiếm, lọc), thêm, sửa, ẩn/hiện, xóa;
 * xử lý ảnh nổi bật và các ảnh nhúng trong nội dung (tải ảnh ngoài/base64 về lưu nội bộ).
 */
class NewsController extends Controller
{
    public function __construct(private ImageCompressionService $imageCompression)
    {
    }

    /** Danh sách bài viết có tìm kiếm và lọc theo loại/trạng thái. */
    public function index(Request $request)
    {
        $query = News::with('author')->latest();

        // Tìm kiếm theo tiêu đề / tóm tắt
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại bài
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $news = $query->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    /** Form tạo bài viết. */
    public function create()
    {
        return view('admin.news.create');
    }

    /** Lưu bài viết mới: tạo slug, lưu ảnh nổi bật, xử lý ảnh trong nội dung và đặt ngày xuất bản. */
    public function store(Request $request)
    {
        $this->validateNewsPayload($request);

        $data = $request->except('featured_image');
        $data['slug'] = Str::slug($request->title) . '-' . uniqid();
        $data['author_id'] = Auth::id();

        if ($request->hasFile('featured_image')) {
            $path = $this->imageCompression->compressAndSave($request->file('featured_image'), 'news');
            $data['featured_image'] = $path;
        }

        // Nếu xuất bản mà chưa có ngày xuất bản thì đặt là hiện tại
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['content'] = $this->processContentImages($request->input('content'));

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Thêm bài viết thành công!');
    }

    /** Form sửa bài viết. */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /** Cập nhật bài viết: đổi slug khi đổi tiêu đề, thay ảnh nổi bật và dọn ảnh nội dung không còn dùng. */
    public function update(Request $request, News $news)
    {
        $this->validateNewsPayload($request);

        $data = $request->except('featured_image');

        if ($request->input('title') !== $news->title) {
            $data['slug'] = Str::slug($request->input('title')) . '-' . uniqid();
        }

        if ($request->hasFile('featured_image')) {
            // Xóa ảnh cũ trước khi lưu ảnh mới
            if ($news->featured_image && \Storage::disk('public')->exists($news->featured_image)) {
                \Storage::disk('public')->delete($news->featured_image);
            }
            $path = $this->imageCompression->compressAndSave($request->file('featured_image'), 'news');
            $data['featured_image'] = $path;
        }

        // Khi chuyển sang xuất bản mà chưa có ngày thì đặt ngày hiện tại
        if ($data['status'] === 'published' && !$news->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $processedContent = $this->processContentImages($request->input('content'));
        $data['content'] = $processedContent;

        // Xóa các ảnh nội dung cũ không còn được dùng nữa
        $oldImages = $this->extractImagePaths($news->content);
        $newImages = $this->extractImagePaths($processedContent);

        $imagesToDelete = array_diff($oldImages, $newImages);
        foreach ($imagesToDelete as $image) {
            if (\Storage::disk('public')->exists($image)) {
                \Storage::disk('public')->delete($image);
            }
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    /** Bật/tắt hiển thị bài viết (published <-> hidden). */
    public function toggleVisibility(News $news)
    {
        $news->status = $news->status === 'published' ? 'hidden' : 'published';
        if ($news->status === 'published' && !$news->published_at) {
            $news->published_at = now();
        }
        $news->save();

        $label = $news->status === 'published' ? 'hiện' : 'ẩn';
        return back()->with('success', "Đã {$label} bài viết \"{$news->title}\".");
    }

    /** Xóa bài viết cùng ảnh nổi bật và các ảnh nhúng trong nội dung. */
    public function destroy(News $news)
    {
        if ($news->featured_image && \Storage::disk('public')->exists($news->featured_image)) {
            \Storage::disk('public')->delete($news->featured_image);
        }
        
        // Xóa các ảnh nhúng trong nội dung bài viết
        $contentImages = $this->extractImagePaths($news->content);
        foreach ($contentImages as $image) {
            if (\Storage::disk('public')->exists($image)) {
                \Storage::disk('public')->delete($image);
            }
        }
        
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Xóa bài viết thành công!');
    }

    /** API tải ảnh cho trình soạn thảo nội dung (dùng bởi editor), trả về URL ảnh đã lưu. */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Loại bỏ file rác (quá nhỏ hoặc không phải ảnh thật)
            if ($file->getSize() < 500) {
                return response()->json(['error' => 'File too small.'], 400);
            }
            if (!str_starts_with($file->getMimeType(), 'image/')) {
                return response()->json(['error' => 'Not an image file.'], 400);
            }
            
            $path = $this->imageCompression->compressAndSave($file, 'news/content');
            return response()->json(['url' => '/storage/' . ltrim($path, '/')]);
        }
        return response()->json(['error' => 'No file uploaded.'], 400);
    }

    /** Trích các đường dẫn ảnh nội dung (thư mục news/content) từ HTML để dọn dẹp khi cần. */
    private function extractImagePaths($content)
    {
        $paths = [];
        if (!$content) return $paths;
        
        preg_match_all('/src="([^"]*storage\/news\/content\/[^"]*)"/i', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                $parts = explode('storage/', $url);
                if (count($parts) > 1) {
                    $paths[] = $parts[1]; 
                }
            }
        }
        return $paths;
    }

    /**
     * Quét ảnh trong nội dung HTML: tải ảnh từ URL ngoài hoặc ảnh base64 về lưu nội bộ,
     * rồi thay src bằng đường dẫn nội bộ (tránh phụ thuộc nguồn ngoài, dễ dọn dẹp).
     */
    private function processContentImages($content)
    {
        if (empty($content)) return $content;

        return preg_replace_callback('/<img[^>]+src="([^"]+)"[^>]*>/i', function ($matches) {
            $imgTag = $matches[0];
            $src = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');

            // TinyMCE lúc thêm tin hay đổi /storage/... thành ../../../storage/...
            if (preg_match('#storage/(news/content/[^"?]+)#i', $src, $pathMatch)) {
                return str_replace($matches[1], '/storage/' . ltrim($pathMatch[1], '/'), $imgTag);
            }

            // Ảnh từ URL ngoài -> tải về lưu nội bộ
            if (str_starts_with($src, 'http')) {
                try {
                    $imageContent = @file_get_contents($src);
                    if ($imageContent) {
                        $extension = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION);
                        if (!$extension || !in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $extension = 'jpg';
                        }
                        $filename = Str::random(40) . '.' . $extension;
                        $path = 'news/content/' . $filename;
                        Storage::disk('public')->put($path, $imageContent);
                        return str_replace($matches[1], '/storage/' . $path, $imgTag);
                    }
                } catch (\Exception $e) {}
            }

            // Ảnh base64 -> giải mã và lưu nội bộ
            if (str_starts_with($src, 'data:image')) {
                preg_match('/data:image\/(.*?);base64,(.*)/', $src, $base64Matches);
                if (count($base64Matches) == 3) {
                    $extension = $base64Matches[1];
                    if ($extension == 'jpeg') $extension = 'jpg';
                    $base64Data = base64_decode($base64Matches[2]);
                    $filename = Str::random(40) . '.' . $extension;
                    $path = 'news/content/' . $filename;
                    Storage::disk('public')->put($path, $base64Data);
                    return str_replace($matches[1], '/storage/' . $path, $imgTag);
                }
            }

            return $imgTag;
        }, $content);
    }

    /** Validate form thêm/sửa tin — nội dung TinyMCE trống hoặc chỉ thẻ rỗng cũng báo lỗi. */
    private function validateNewsPayload(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'type' => 'required|in:news,guide,announcement,event',
            'status' => 'required|in:draft,published,hidden',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:20480',
            'published_at' => 'nullable|date',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',
            'type.required' => 'Vui lòng chọn loại bài viết.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'featured_image.image' => 'Ảnh đại diện phải là file hình ảnh.',
            'featured_image.max' => 'Ảnh đại diện không được lớn hơn 20MB.',
        ]);

        $validator->after(function ($v) use ($request) {
            if ($this->isContentEmpty($request->input('content'))) {
                $v->errors()->add('content', 'Vui lòng nhập nội dung chi tiết.');
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function isContentEmpty(?string $content): bool
    {
        if ($content === null || trim($content) === '') {
            return true;
        }

        $text = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\x{00a0}/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return $text === '';
    }
}
