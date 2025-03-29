<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index()
    {

        $users = User::where('id', '!=', Auth::id())->get();

        $userProfileLinks = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'avatar' => $user->avatar,
                'profileLink' => url("/profile/{$user->id}"),
            ];
        });

        return response()->json([
            'users' => $userProfileLinks,
        ]);
    }

    public function addFriend(Request $request, $friendId)
    {
        $loggedInUser  = $request->user();



        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не аутентифицирован'], 401);
        }


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

}
