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
        $newsList = News::where('status', 'published')
                    ->where('type', '!=', 'event')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
                    
        $popularNews = News::where('status', 'published')
                    ->where('type', '!=', 'event')
                    ->orderBy('view_count', 'desc')
                    ->take(5)
                    ->get();
                    
        $upcomingEvents = \App\Models\Event::where('status', 'active')
                    ->where('start_time', '>=', now())
                    ->orderBy('start_time')
                    ->take(4)
                    ->get();
                    
        return view('client.news.index', compact('newsList', 'popularNews', 'upcomingEvents'));
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
