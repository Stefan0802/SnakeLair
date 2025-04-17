<?php

namespace App\Http\Controllers;

use App\Models\Likes;
use App\Models\Post;
use App\Models\Comment;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Requests\EditPostRequest;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\AddCommentRequest;
class PostsController extends Controller
{
    public function posts(Request $request)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
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
                'created' => $post->created_at,
                'update' => $post->created_at,
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



    public function createPost(CreatePostRequest $request)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        $post = Post::create([
            'user_id' => $loggedInUser ->id,
            'title' => $request->validated()['title'],
            'description' => $request->validated()['description'],
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

    public function addComment(AddCommentRequest $request, $id)
    {

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $id,
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
            return response()->json(['status' => 'success', 'message' => 'Лайк убран'], 200);
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
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        // Находим пост по ID
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Пост не найден'], 404);
        }

        // Проверяем, является ли пользователь владельцем поста или администратором
        if ($post->user_id !== $loggedInUser ->id || !$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Удаляем пост
        $post->delete();

        return response()->json(['status' => 'success', 'message' => 'Пост удален'], 200);
    }


    public function updatePost(EditPostRequest $request, $id)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Пост не найден'], 404);
        }

        if ($post->user_id !== $loggedInUser ->id) {
            return response()->json(['status' => 'error', 'message' => 'это не ваш пост'], 403);
        }

        // Обновляем пост
        $post->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Пост изменен', 'post' => $post], 200);
    }

}
