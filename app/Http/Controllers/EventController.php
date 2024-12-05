<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Display the calendar with events.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $events = Event::select('id', 'title', 'start_time as start', 'end_time as end')->get();
            return response()->json($events);
        }
        return view('fullcalender');
    }

    /**
     * Handle AJAX requests for creating, updating, and deleting events.
     */
    public function ajax(Request $request): JsonResponse
    {
        switch ($request->type) {
            case 'add':
                $event = Event::create([
                    'title'      => $request->title,
                    'start_time' => $request->start,
                    'end_time'   => $request->end,
                ]);
                return response()->json($event);

            case 'update':
                $event = Event::find($request->id);
                if ($event) {
                    $event->update([
                        'title'      => $request->title,
                        'start_time' => $request->start,
                        'end_time'   => $request->end,
                    ]);
                }
                return response()->json($event);

            case 'delete':
                $event = Event::find($request->id);
                if ($event) {
                    $event->delete();
                }
                return response()->json(['status' => 'Event Deleted Successfully']);

            default:
                return response()->json(['error' => 'Invalid Request'], 400);
        }
    }
}

