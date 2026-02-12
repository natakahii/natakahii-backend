<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiRecommendationEvent;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    /**
     * Quick ask (public, no conversation history saved for guests).
     */
    public function ask(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $reply = 'Thank you for your question! Our AI shopping assistant is being configured. '.
            'In the meantime, try browsing our product catalog for great deals.';

        return response()->json([
            'reply' => $reply,
        ]);
    }

    /**
     * List conversations for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $conversations = AiConversation::where('user_id', $request->user('api')->id)
            ->withCount('messages')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    /**
     * Create a new AI conversation.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $conversation = AiConversation::create([
            'user_id' => $request->user('api')->id,
            'title' => $request->title ?? 'New conversation',
        ]);

        return response()->json([
            'message' => 'Conversation created.',
            'conversation' => $conversation,
        ], 201);
    }

    /**
     * Show a conversation with its messages.
     */
    public function show(AiConversation $conversation, Request $request): JsonResponse
    {
        if ($conversation->user_id !== $request->user('api')->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $conversation->load('messages');

        return response()->json([
            'conversation' => $conversation,
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(AiConversation $conversation, Request $request): JsonResponse
    {
        if ($conversation->user_id !== $request->user('api')->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        $assistantReply = 'I appreciate your message! The AI assistant is being integrated. '.
            'Check back soon for personalized product recommendations.';

        $assistantMessage = AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $assistantReply,
        ]);

        return response()->json([
            'reply' => $assistantMessage,
        ]);
    }

    /**
     * Track a recommendation event.
     */
    public function trackRecommendationEvent(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'ai_conversation_id' => ['nullable', 'exists:ai_conversations,id'],
            'event_type' => ['required', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
        ]);

        AiRecommendationEvent::create([
            'user_id' => $request->user('api')->id,
            'product_id' => $request->product_id,
            'ai_conversation_id' => $request->ai_conversation_id,
            'event_type' => $request->event_type,
            'metadata' => $request->metadata,
        ]);

        return response()->json([
            'message' => 'Event tracked.',
        ]);
    }

    /**
     * Get personalized recommendations.
     */
    public function recommendations(Request $request): JsonResponse
    {
        $products = Product::query()
            ->where('status', 'active')
            ->with('vendor', 'images')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return response()->json([
            'recommendations' => ProductResource::collection($products),
        ]);
    }
}
