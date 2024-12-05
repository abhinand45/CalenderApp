<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $events = Event::where('user_id', Auth::id())->select('id', 'title', 'start_time as start', 'end_time as end','user_id')->get();
            return response()->json($events);
        }
        return view('fullcalender');
    }


    public function ajax(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'start' => 'required|date|before_or_equal:end',
            'end'   => 'required|date|after_or_equal:start',
        ];

        $messages = [
            'title.required' => 'The event title is required.',
            'start.required' => 'The start date and time are required.',
            'end.required'   => 'The end date and time are required.',
            'start.before_or_equal' => 'The start time must be before or equal to the end time.',
            'end.after_or_equal' => 'The end time must be after or equal to the start time.',
        ];


        if (in_array($request->type, ['add', 'update'])) {
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        }

        switch ($request->type) {
            case 'add':
                return $this->addEvent($request);
            case 'update':
                return $this->updateEvent($request);
            case 'delete':
                return $this->deleteEvent($request);
            default:
                return response()->json(['error' => 'Invalid request type'], 400);
        }
    }


    public function addEvent(Request $request)
    {


        $event = new Event();
        $event->title = $request->title;
        $event->start_time = Carbon::parse($request->start);
        $event->end_time = Carbon::parse($request->end);
        $event->user_id = Auth::id();
        $event->save();

        return response()->json(['success' => 'Event added successfully']);
    }


    public function updateEvent(Request $request)
    {

        $event = Event::where('user_id', Auth::id())->find($request->id);
        if (!$event) {
            return response()->json(['error' => 'Event not found or you do not have permission to edit this event'], 404);
        }

        $event->title = $request->title;
        $event->start_time = Carbon::parse($request->start);
        $event->end_time = Carbon::parse($request->end);
        $event->save();

        return response()->json(['success' => 'Event updated successfully']);
    }


    public function deleteEvent(Request $request)
    {
        
        $event = Event::where('user_id', Auth::id())->find($request->id);
        if (!$event) {
            return response()->json(['error' => 'Event not found or you do not have permission to delete this event'], 404);
        }

        $event->delete();
        return response()->json(['success' => 'Event deleted successfully']);
    }
}
