<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
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
