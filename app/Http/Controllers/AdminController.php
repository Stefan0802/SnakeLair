<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function posts(Request $request)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        if ($loggedInUser ->isAdmin()) {
            $posts = Post::with('user')->get()->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'description' => $post->description,
                    'user' => $post->user->firstName,
                ];
            });

            return response()->json([
                'status' => 'success',
                'posts' => $posts,
            ], 200);
        }

        return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
    }

    public function postDel(Request $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        if($loggedInUser->isAdmin()){

            $post = Post::find($id);
            $post ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Пост успешно удален',
            ]);
        }
    }

    public function editPost(Request $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Пост не найден'], 404);
        }

        if (!$loggedInUser->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        // Валидация данных
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Обновляем пост
        $post->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Пост изменен', 'post' => $post], 200);
    }


    // пользователи

    public function getUsers(Request $request)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        // Получаем всех пользователей
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'role' => $user->role,
            ];
        });

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ], 200);
    }

    public function editUser(Request $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        $user = User::find($id);

        $validated = $request->validate([
            'firstName' => 'sometimes|required|string|max:255',
            'lastName' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,',
            'role' => 'sometimes|required'
        ]);

        if ($validated){
            if ($request->has('firstName')) {
                $user->firstName = $request->firstName;
            }
            if ($request->has('lastName')) {
                $user->lastName = $request->lastName;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('role')) {
                $user->role = $request->role;
            }
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Данные изменены',
            'user' => $user
        ], 200);
    }


}
