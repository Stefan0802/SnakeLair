<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public static function addComment($request, $id): JsonResponse
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

    public static function editComment($request, $id): JsonResponse
    {
        $comment = Comment::find($id);

        if(!$comment){
            return response()->json([
                'status' => 'error',
                'message' => 'комментарий не найден'
            ], 404);
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Комментарий изменен',
            'comment' => $comment
        ], 200);
    }

    public static function delComment($request, $id): JsonResponse
    {
        $comment = Comment::find($id);

        if(!$comment){
            return response()->json([
                'status' => 'error',
                'message' => 'комментарий не найден'
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'комментарий удален'
        ],200);
    }
}
