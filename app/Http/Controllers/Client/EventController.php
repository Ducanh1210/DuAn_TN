<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        // Sắp xếp ưu tiên: đang diễn ra, sắp diễn ra, đã kết thúc
        $events = Event::where('status', '!=', 'hidden')
                    ->orderByRaw("CASE 
                        WHEN start_time <= ? AND end_time >= ? THEN 1 
                        WHEN start_time > ? THEN 2 
                        ELSE 3 END", [$now, $now, $now])
                    ->orderBy('start_time', 'asc')
                    ->paginate(9);
                    
        return view('client.events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
                    ->where('status', '!=', 'hidden')
                    ->firstOrFail();
                    
        $relatedEvents = Event::where('status', '!=', 'hidden')
                            ->where('id', '!=', $event->id)
                            ->orderBy('start_time', 'desc')
                            ->take(3)
                            ->get();
                            
        return view('client.events.show', compact('event', 'relatedEvents'));
    }
}
