<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'like',
                    '%' . $search . '%'
                )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'location',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'event_date',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {
            $query->whereDate(
                'event_date',
                '<=',
                $request->date_to
            );
        }

        $events = $query
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'message' => 'Events retrieved successfully.',
            'data' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'event_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:start_time',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::in([
                    'scheduled',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => $event,
        ], 201);
    }

    public function show(Event $event)
    {
        return response()->json([
            'message' => 'Event retrieved successfully.',
            'data' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'event_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:start_time',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'scheduled',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Event updated successfully.',
            'data' => $event,
        ]);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully.',
        ]);
    }
}
