<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(Request $request)
    {
        // Fetch events based on the selected start and end dates
        if ($request->ajax()) {
            $data = Event::whereDate('start_time', '>=', $request->start)
                         ->whereDate('end_time', '<=', $request->end)
                         ->get(['id', 'title', 'start_time', 'end_time']);

            return response()->json($data);
        }

        // Fetch all events for the page load and send them to the view
        $events = Event::all();
        return view('fullcalender', compact('events'));
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function ajax(Request $request): JsonResponse
    {

        switch ($request->type) {
           case 'add':
              $event = Event::create([
                  'title' => $request->title,
                  'start_time' => $request->start,
                  'end_time' => $request->end,
              ]);

              return response()->json($event);
             break;

           case 'update':


            $event = Event::find($request->id);
            if ($event) {
                $event->title = $request->title;
                $event->start_time = $request->start;
                $event->end_time = $request->end;
                $event->save();
            }
            return response()->json($event);
            break;

           case 'delete':
              $event = Event::find($request->id)->delete();

              return response()->json($event);
             break;

           default:
             break;
        }
    }
}
