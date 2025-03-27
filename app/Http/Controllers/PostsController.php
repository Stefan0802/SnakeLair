<?php

namespace App\Http\Controllers;

use App\Models\Post;
use http\Env\Response;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function posts(Request $request)
    {
        $loggedInUser  = $request->user();

        // Проверяем, аутентифицирован ли пользователь
        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }

        // Получаем все посты
        $posts = Post::with('user')->get(); // Загружаем посты с информацией о пользователе

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
