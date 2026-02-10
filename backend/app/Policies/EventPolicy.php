<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * User csak saját eventjét nézheti
     */
    public function view(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    /**
     * User csak saját eventjét frissítheti
     */
    public function update(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    /**
     * User csak saját eventjét törölheti
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }
}
