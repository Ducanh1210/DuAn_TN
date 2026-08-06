<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Location;
use App\Models\News;

class LandingController extends Controller
{
    public function index()
    {
        $featuredLocations = Location::query()
            ->where('status', 'published')
            ->with(['category', 'images'])
            ->withCount('panoramas')
            ->orderByDesc('view_count')
            ->take(6)
            ->get()
            ->each(fn ($loc) => $loc->setAttribute('display_thumbnail', $loc->resolveThumbnailUrl()));

        $latestNews = News::query()
            ->where('status', 'published')
            ->where('type', '!=', 'event')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $upcomingEvents = Event::query()
            ->where('status', 'active')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(3)
            ->get();

        $stats = [
            'locations' => Location::where('status', 'published')->count(),
            'news' => News::where('status', 'published')->count(),
            'events' => Event::where('status', 'active')->count(),
        ];

        return view('client.landing', compact(
            'featuredLocations',
            'latestNews',
            'upcomingEvents',
            'stats'
        ));
    }
}
