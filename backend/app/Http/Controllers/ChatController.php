<?php
namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Conversation;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Üzenet küldése
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = auth('api')->id();

        $conversation = $this->chatService->sendUserMessage(
            $userId,
            $request->content
        );

        return response()->json($conversation);
    }


    private function getOrCreateActiveConversation(int $userId): Conversation
    {
        $conversation = Conversation::where('user_id', $userId)
            ->whereIn('status', ['open', 'pending', 'assigned'])
            ->latest('updated_at')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'status' => 'open',
            ]);
        }

        return $conversation;
    }


    /**
     * User saját beszélgetésének lekérése
     */
    public function getConversation()
    {
        $userId = auth('api')->id();
        $conversation = $this->getOrCreateActiveConversation($userId);
        $conversation->load('messages.sender');
        return response()->json($conversation);
    }

    /**
     * Agent: összes beszélgetés listázása
     */
    public function listConversations()
    {
        $conversations = Conversation::with(['user', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])
        ->whereIn('status', ['open', 'pending'])
        ->orWhere('handoff_requested', true)
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json($conversations);
    }

    /**
     * Agent: beszélgetés részleteinek lekérése
     */
    public function getConversationById($id)
    {
        $conversation = Conversation::with(['user', 'messages.sender'])->findOrFail($id);
        return response()->json($conversation);
    }

    /**
     * Agent: üzenet küldése
     */
    public function sendAgentMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = $this->chatService->sendAgentMessage(
            auth('api')->id(),
            $conversationId,
            $request->content
        );

        return response()->json($message);
    }

    /**
     * Agent: beszélgetés lezárása
     */
    public function closeConversation($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->update(['status' => 'closed']);

        return response()->json(['message' => 'Conversation closed']);
    }
}
