<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Likes::class);
    }

    public static function posts($request): JsonResponse
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

    public static function createPost($request): JsonResponse
    {
        $loggedInUser  = $request->user();

        $post = Post::create([
            'user_id' => $loggedInUser ->id,
            'title' => $request->validated()['title'],
            'description' => $request->validated()['description'],
        ]);

        // Загружаем изображение и обновляем пост
        $imageResponse = self::uploadImage($request);
        if ($imageResponse->getStatusCode() === 200) {
            $post->photo = json_decode($imageResponse->getContent())->photo; // Получаем путь к изображению
            $post->save();
        }

        return response()->json(['status' => 'success', 'post' => $post], 201);
    }

    public static function uploadImage($request): JsonResponse
    {
        $loggedInUser  = Auth::user();

        // Получаем пост, если он существует
        $post = Post::where('user_id', $loggedInUser ->id)->latest()->first(); // Или используйте другой способ получения поста

        if ($post) {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '.' . $file->getClientOriginalExtension(); // Генерация уникального имени файла
                $file->move(public_path('uploads/images'), $filename); // Перемещение файла в папку

                // Обновляем поле photo в посте
                $post->photo = 'uploads/images/' . $filename;
                $post->save();

                $response = ['status' => 'success', 'photo' => $post->photo];
                return response()->json($response, 200);
            }
        }

        $response = ['status' => 'error', 'message' => 'Пост не найден или файл не загружен'];
        return response()->json($response, 404);
    }


    public static function deletePost($request, $id): JsonResponse
    {
        $loggedInUser  = $request->user();

        // Находим пост по ID
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Пост не найден'], 404);
        }

        if ($post->user_id !== $loggedInUser ->id ) {
            return response()->json(['status' => 'error', 'message' => 'Не ваш пост'], 403);
        }

        $post->delete();

        return response()->json(['status' => 'success', 'message' => 'Пост удален'], 200);
    }

    public static function updatePost( $request, $id): JsonResponse
    {
        $loggedInUser  = $request->user();

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Пост не найден'], 404);
        }

        if ($post->user_id !== $loggedInUser ->id) {
            return response()->json(['status' => 'error', 'message' => 'это не ваш пост'], 403);
        }

        $post->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Пост изменен', 'post' => $post], 200);
    }

    public static function post($request): JsonResponse
    {
        $loggedInUser  = $request->user();

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

    public static function postDel( $request, $id): JsonResponse
    {
        $loggedInUser  = $request->user();

        if($loggedInUser->isAdmin()){

            $post = Post::find($id);
            $post ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Пост успешно удален',
            ]);
        }
    }

    public static function editPost( $request, $id): JsonResponse
    {
        $loggedInUser  = $request->user();

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
}
