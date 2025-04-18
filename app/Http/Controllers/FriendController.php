<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class FriendController extends Controller
{

    public function index()
    {
        return Friend::index();
    }

    public function addFriend(Request $request, $friendId)
    {
        return Friend::addFriend($request, $friendId);
    }

    public function removeFriend(Request $request, $friendId)
    {
        return Friend::removeFriend($request, $friendId);
    }



}
