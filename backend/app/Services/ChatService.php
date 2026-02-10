<?php
namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;

class ChatService
{
    protected $botService;

    public function __construct(BotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * User üzenet elküldése és bot/handoff feldolgozás
     */
    public function sendUserMessage(int $userId, string $content)
    {
        // Conversation lekérése vagy létrehozása
        $conversation = Conversation::where('user_id', $userId)
            ->where('status', '!=', 'closed')
            ->latest('updated_at')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'status' => 'bot', // indulhat bottal, ahogy eddig
                'handoff_requested' => false,
            ]);
        }

        // User üzenet mentése
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => $userId,
            'content' => $content,
        ]);

        // Broadcasting: user üzenet
        broadcast(new MessageSent($userMessage));

        // Bot válasz generálása
        $botResponse = $this->botService->generateResponse($content);

        if ($botResponse === null) {
            // Handoff szükséges
            $conversation->update([
                'handoff_requested' => true,
                'status' => 'open',
            ]);

            // TODO nyelvi kulcsokat és konstansokat haszánlni
            $botMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'sender_id' => null,
                'content' => 'Átirányítom egy ügyintézőhöz. Kérlek, várj egy kicsit.',
            ]);

            broadcast(new MessageSent($botMessage));
        } else {
            // Bot válasz
            $botMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'sender_id' => null,
                'content' => $botResponse,
            ]);

            broadcast(new MessageSent($botMessage));
        }

        return $conversation->fresh()->load('messages.sender');
    }

    /**
     * Agent üzenet küldése
     */
    public function sendAgentMessage(int $agentId, int $conversationId, string $content)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Ha még nincs hozzárendelve agent, most hozzárendeljük
        if (!$conversation->assigned_agent_id) {
            $conversation->update([
                'assigned_agent_id' => $agentId,
                'status' => 'pending',
            ]);
        }

        $agentMessage = Message::create([
            'conversation_id' => $conversationId,
            'sender_type' => 'agent',
            'sender_id' => $agentId,
            'content' => $content,
        ]);

        broadcast(new MessageSent($agentMessage))->toOthers();

        return $agentMessage;
    }
}
