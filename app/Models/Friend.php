<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Friend extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public static function index(): JsonResponse
    {

        $users = User::where('id', '!=', Auth::id())->get();

        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'avatar' => $user->avatar,
            ];
        });

        return response()->json([
            'users' => $users,
        ]);
    }

    public static function addFriend($request, $friendId): JsonResponse
    {
        $loggedInUser  = $request->user();

        $friend = User::find($friendId);
        if (!$friend) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не найден'], 404);
        }

        if (Friend::where('user_id', $loggedInUser ->id)->where('friend_id', $friendId)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Друг уже добавлен'], 400);
        }

        Friend::create([
            'user_id' => $loggedInUser ->id,
            'friend_id' => $friendId,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Друг добавлен!'], 201);
    }

    public static function removeFriend(Request $request, $friendId): JsonResponse
    {
        $loggedInUser  = $request->user();

        // Проверяем, существует ли друг
        $friend = Friend::where('user_id', $loggedInUser ->id)->where('friend_id', $friendId)->first();
        if (!$friend) {
            return response()->json(['status' => 'error', 'message' => 'Друг не найден'], 404);
        }

        $friend->delete();

        return response()->json(['status' => 'success', 'message' => 'Друг удалён!'], 200);
    }
}
