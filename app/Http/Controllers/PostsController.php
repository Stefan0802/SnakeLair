<?php

namespace App\Http\Controllers;

use App\Models\Likes;
use App\Models\Post;
use App\Models\Comment;
use http\Env\Response;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function posts(Request $request)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }

        // Загружаем посты с пользователями, комментариями и лайками
        $posts = Post::with(['user', 'comment.user', 'likes'])->get();

        // Добавляем информацию о количестве лайков и статусе лайка для текущего пользователя
        $posts = $posts->map(function ($post) use ($loggedInUser ) {
            $likesCount = $post->likes->count();
            $userLiked = $post->likes->contains('user_id', $loggedInUser ->id);

            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'photo' => $post->photo,
                'user' => $post->user,
                'comments' => $post->comment,
                'likes_count' => $likesCount,
                'user_liked' => $userLiked,
                'profileLink' => url("/profile/{$loggedInUser ->id}"),
            ];
        });

        return response()->json([
            'status' => 'success',
            'posts' => $posts,

        ], 200);
    }



    public function createPost(Request $request)
    {

        $loggedInUser  = $request->user();


        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }


        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        $post = Post::create([
            'user_id' => $loggedInUser ->id,
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'photo' => $this->uploadImage($request),

        ]);

        return response()->json(['status' => 'success', 'post' => $post], 201);
    }


    private function uploadImage(Request $request)
    {
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('images/posts', 'public');
            return $imagePath;
        }
        return null;
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $request->post_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => 'success',
            'comment' => $comment,
        ], 201);
    }

    public function toggleLike(Request $request, $id)
    {
        // Получаем текущего пользователя
        $userId = $request->user()->id;

        // Проверяем, есть ли уже лайк от этого пользователя на данном посте
        $like = Likes::where('user_id', $userId)
            ->where('post_id', $id) // Исправлено: используем 'post_id' вместо $id
            ->first();

        if ($like) {
            // Если лайк уже существует, то убираем его
            $like->delete();
            return response()->json(['status' => 'success', 'message' => 'Like removed'], 200);
        } else {
            // Если лайка нет, то создаем новый
            $like = Likes::create([
                'user_id' => $userId,
                'post_id' => $id, // Исправлено: используем $id вместо $request->post_id
            ]);
            return response()->json(['status' => 'success', 'like' => $like], 201);
        }
    }


    public function deletePost(Request $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }

        // Находим пост по ID
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Post not found'], 404);
        }

        // Проверяем, является ли пользователь владельцем поста или администратором
        if ($post->user_id !== $loggedInUser ->id && !$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Удаляем пост
        $post->delete();

        return response()->json(['status' => 'success', 'message' => 'Post deleted successfully'], 200);
    }


    public function updatePost(Request $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }

        // Находим пост по ID
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Post not found'], 404);
        }

        // Проверяем, является ли пользователь владельцем поста или администратором
        if ($post->user_id !== $loggedInUser ->id && !$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
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

        return response()->json(['status' => 'success', 'message' => 'Post updated successfully', 'post' => $post], 200);
    }





}
