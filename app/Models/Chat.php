<?php

namespace App\Models;

use App\Http\Requests\ChatRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class Chat extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function sendMessage($request, $id): JsonResponse
    {

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $id,
            'message' => $request->message,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Сообщение отправлено', 'chat' => $chat], 201);
    }

    public static function getMessages($receiverId): JsonResponse
    {
        $userId = Auth::id();

        // Получаем сообщения между текущим пользователем и получателем
        $messages = Chat::where(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $userId) // где юзер айди это айди отправителя
            ->where('receiver_id', $receiverId); // где айдишник собиседника это получатель
        })->orWhere(function ($query) use ($userId, $receiverId) { // или наоборот
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $userId);
        })->get();

        return response()->json(['status' => 'success', 'messages' => $messages], 200);
    }

    public static function chat(): JsonResponse
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
                        'name' => $otherUser->firstName,
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
