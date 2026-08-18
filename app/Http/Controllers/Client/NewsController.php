<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

/**
 * Controller trang tin tức phía người dùng: danh sách bài viết (trừ loại sự kiện),
 * bài xem nhiều và trang chi tiết kèm bài liên quan.
 */
class NewsController extends Controller
{
    /** Danh sách tin tức + bài phổ biến + vài sự kiện sắp diễn ra cho sidebar. */
    public function index()
    {
        return redirect()->route('client.events.index');
    }

    /** Trang chi tiết một bài viết kèm các bài liên quan. */
    public function show($slug)
    {
        $news = News::where('slug', $slug)
                    ->where('status', 'published')
                    ->where('type', '!=', 'event')
                    ->firstOrFail();
                    
        $relatedNews = News::where('status', 'published')
                            ->where('type', '!=', 'event')
                            ->where('id', '!=', $news->id)
                            ->orderBy('published_at', 'desc')
                            ->take(3)
                            ->get();
                            
        return view('client.news.show', compact('news', 'relatedNews'));
    }
}
