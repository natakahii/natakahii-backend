<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    /**
     * Start a new conversation.
     */
    public function startConversation(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'order_id' => ['nullable', 'exists:orders,id'],
        ]);

        $user = $request->user('api');

        $conversation = Conversation::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'order_id' => $request->order_id,
            'subject' => $request->subject,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $request->message,
        ]);

        return response()->json([
            'message' => 'Conversation started.',
            'conversation' => $conversation->load('messages.user', 'sender', 'receiver'),
        ], 201);
    }
}
