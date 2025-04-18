<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class Likes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    /**
     * Связь с моделью User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с моделью Post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public static function toggleLike($request, $id): JsonResponse
    {
        // Получаем текущего пользователя
        $userId = $request->user()->id;


        $like = Likes::where('user_id', $userId)
            ->where('post_id', $id)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json(['status' => 'success', 'message' => 'Лайк убран'], 200);
        } else {
            $like = Likes::create([
                'user_id' => $userId,
                'post_id' => $id,
            ]);
            return response()->json(['status' => 'success', 'like' => $like], 201);
        }
    }
}
