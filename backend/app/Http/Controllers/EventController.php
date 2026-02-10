<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * User saját eventjeinek listázása
     */
    public function index()
    {
        $events = auth('api')->user()->events()->orderBy('occurs_at', 'asc')->get();
        return response()->json($events);
    }

    /**
     * Új event létrehozása
     */
    public function store(StoreEventRequest $request)
    {
        $event = auth('api')->user()->events()->create($request->validated());
        return response()->json($event, 201);
    }

    /**
     * Egy adott event megjelenítése
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);
        return response()->json($event);
    }

    /**
     * Event frissítése (csak description)
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $event->update($request->validated());
        return response()->json($event);
    }

    /**
     * Event törlése
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
}