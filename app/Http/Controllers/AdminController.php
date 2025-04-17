<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\EditAdminUserRequest;
use App\Http\Requests\EditAdminPostRequest;


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

    public function editPost(EditAdminPostRequest $request, $id)
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

    public function editUser(EditAdminUserRequest $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        $user = User::find($id);



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

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Данные изменены',
            'user' => $user
        ], 200);
    }

    public function delUser(Request $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        $user = User::find($id);

        $user -> delete();

        return response()->json([
            'status'=>'success',
            'message'=>'пользователь удален'
        ]);
    }


}
