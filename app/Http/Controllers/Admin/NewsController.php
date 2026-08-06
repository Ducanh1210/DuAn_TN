<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function __construct(private ImageCompressionService $imageCompression)
    {
    }

    public function index(Request $request)
    {
        $query = News::with('author')->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $news = $query->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'type' => 'required|in:news,guide,announcement,event',
            'status' => 'required|in:draft,published,hidden',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:20480',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = Str::slug($request->title) . '-' . uniqid();
        $data['author_id'] = Auth::id();

        if ($request->hasFile('featured_image')) {
            $path = $this->imageCompression->compressAndSave($request->file('featured_image'), 'news');
            $data['featured_image'] = $path;
        }

        // If status is published and no published_at date, set it to now
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['content'] = $this->processContentImages($request->input('content'));

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'type' => 'required|in:news,guide,announcement,event',
            'status' => 'required|in:draft,published,hidden',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:20480',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->except('featured_image');

        if ($request->input('title') !== $news->title) {
            $data['slug'] = Str::slug($request->input('title')) . '-' . uniqid();
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($news->featured_image && \Storage::disk('public')->exists($news->featured_image)) {
                \Storage::disk('public')->delete($news->featured_image);
            }
            $path = $this->imageCompression->compressAndSave($request->file('featured_image'), 'news');
            $data['featured_image'] = $path;
        }

        // If transitioning to published and no published_at, set it
        if ($data['status'] === 'published' && !$news->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $processedContent = $this->processContentImages($request->input('content'));
        $data['content'] = $processedContent;

        // Handle unused content images deletion
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

    /**
     * Toggle visibility (published <-> hidden)
     */
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

    public function destroy(News $news)
    {
        if ($news->featured_image && \Storage::disk('public')->exists($news->featured_image)) {
            \Storage::disk('public')->delete($news->featured_image);
        }
        
        // Delete images in content
        $contentImages = $this->extractImagePaths($news->content);
        foreach ($contentImages as $image) {
            if (\Storage::disk('public')->exists($image)) {
                \Storage::disk('public')->delete($image);
            }
        }
        
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Xóa bài viết thành công!');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Reject junk files (too small or not a real image)
            if ($file->getSize() < 500) {
                return response()->json(['error' => 'File too small.'], 400);
            }
            if (!str_starts_with($file->getMimeType(), 'image/')) {
                return response()->json(['error' => 'Not an image file.'], 400);
            }
            
            $path = $this->imageCompression->compressAndSave($file, 'news/content');
            return response()->json(['url' => Storage::url($path)]);
        }
        return response()->json(['error' => 'No file uploaded.'], 400);
    }

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

    private function processContentImages($content)
    {
        if (empty($content)) return $content;

        return preg_replace_callback('/<img[^>]+src="([^"]+)"[^>]*>/i', function ($matches) {
            $imgTag = $matches[0];
            $src = $matches[1];

            // Local image, skip
            if (str_starts_with($src, '/') || str_contains($src, request()->getHost())) {
                return $imgTag;
            }

            // External URL
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
                        return str_replace($src, Storage::url($path), $imgTag);
                    }
                } catch (\Exception $e) {}
            }
            
            // Base64
            if (str_starts_with($src, 'data:image')) {
                preg_match('/data:image\/(.*?);base64,(.*)/', $src, $base64Matches);
                if (count($base64Matches) == 3) {
                    $extension = $base64Matches[1];
                    if ($extension == 'jpeg') $extension = 'jpg';
                    $base64Data = base64_decode($base64Matches[2]);
                    $filename = Str::random(40) . '.' . $extension;
                    $path = 'news/content/' . $filename;
                    Storage::disk('public')->put($path, $base64Data);
                    return str_replace($src, Storage::url($path), $imgTag);
                }
            }

            return $imgTag;
        }, $content);
    }
}
