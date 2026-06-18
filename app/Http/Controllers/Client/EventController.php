<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $events = News::where('type', 'event')
                    ->where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->paginate(9);
                    
        return view('client.events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = News::where('slug', $slug)
                    ->where('type', 'event')
                    ->where('status', 'published')
                    ->firstOrFail();
                    
        $relatedEvents = News::where('type', 'event')
                            ->where('status', 'published')
                            ->where('id', '!=', $event->id)
                            ->orderBy('published_at', 'desc')
                            ->take(3)
                            ->get();
                            
        return view('client.events.show', compact('event', 'relatedEvents'));
    }
}
