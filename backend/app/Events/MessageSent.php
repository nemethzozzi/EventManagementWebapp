<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    /**
     * Broadcasting csatorna
     */
    public function broadcastOn()
    {
        return new Channel('conversation.' . $this->message->conversation_id);
    }

    /**
     * Broadcast esemény neve
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
