<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'upcoming');

        $baseQuery = Event::query()->where('status', 'active');

        if ($tab === 'past') {
            $events = (clone $baseQuery)
                ->where('start_time', '<', now())
                ->orderByDesc('start_time')
                ->paginate(9)
                ->withQueryString();
            $featured = null;
        } else {
            $tab = 'upcoming';
            $featured = (clone $baseQuery)
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->first();

            $events = (clone $baseQuery)
                ->where('start_time', '>=', now())
                ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
                ->orderBy('start_time')
                ->paginate(9)
                ->withQueryString();
        }

        return view('client.events.index', compact('events', 'featured', 'tab'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedEvents = Event::where('status', 'active')
            ->where('id', '!=', $event->id)
            ->orderByDesc('start_time')
            ->take(4)
            ->get();

        return view('client.events.show', compact('event', 'relatedEvents'));
    }
}
