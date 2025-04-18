<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChatRequest;
class ChatController extends Controller
{
    // Метод для отправки сообщения
    public function sendMessage(ChatRequest $request, $id)
    {
        return Chat::sendMessage($request, $id);
    }

    // Метод для получения сообщений между пользователями
    public function getMessages(Request $receiverId)
    {
        return Chat::getMessages($receiverId);
    }

    public function chat()
    {
        return Chat::chat();
    }

}


