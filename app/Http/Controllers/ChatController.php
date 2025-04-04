<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Метод для отправки сообщения
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Message sent', 'chat' => $chat], 201);
    }

    // Метод для получения сообщений между пользователями
    public function getMessages($receiverId)
    {
        $userId = Auth::id();

        // Получаем сообщения между текущим пользователем и получателем
        $messages = Chat::where(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $userId);
        })->get();

        return response()->json(['status' => 'success', 'messages' => $messages], 200);
    }

    public function chat()
    {
        $userId = Auth::id();

        $chats = Chat::with(['sender:id,firstName', 'receiver:id,firstName'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->unique(function ($chat) use ($userId) {
                return $chat->sender_id === $userId
                    ? $chat->receiver_id
                    : $chat->sender_id;
            })
            ->map(function ($chat) use ($userId) {
                $otherUser = $chat->sender_id === $userId
                    ? $chat->receiver
                    : $chat->sender;

                return [
                    'user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->firstName, // Используем username вместо name
                    ],
                    'last_message' => [
                        'text' => $chat->message,
                        'time' => $chat->created_at,
                        'is_me' => $chat->sender_id === $userId,
                    ],
                ];
            })
            ->values();

        return response()->json(['status' => 'success', 'chats' => $chats], 200);
    }
}


