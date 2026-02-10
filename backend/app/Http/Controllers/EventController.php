<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * User saját eseményei listázása
     *
     * @return JsonResponse
     */
    public function index()
    {
        $events = auth('api')->user()->events()->orderBy('occurs_at', 'asc')->get();

        return response()->json($events);
    }

    /**
     * Új esemény létrehozása
     *
     * @return JsonResponse
     */
    public function store(StoreEventRequest $request)
    {
        $event = auth('api')->user()->events()->create($request->validated());

        return response()->json([
            'message_key' => 'events_page.response.success.event.created',
            'event' => $event,
        ], 201);
    }

    /**
     * Egy adott esemény létrehozása
     *
     * @return JsonResponse
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        return response()->json($event);
    }

    /**
     * Esemény frissítése (csak description)
     *
     * @return JsonResponse
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $event->update($request->validated());

        return response()->json([
            'message_key' => 'events_page.response.success.event.updated',
            'event' => $event->fresh(),
        ]);
    }

    /**
     * Esemény törlése
     *
     * @return JsonResponse
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return response()->json([
            'message_key' => 'events_page.response.success.event.deleted',
        ]);
    }
}
