<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EditAdminUserRequest;
use App\Http\Requests\EditAdminPostRequest;


class AdminController extends Controller
{

    public function posts(Request $request)
    {
        return Post::post($request);
    }

    public function postDel(Request $request, $id)
    {
        return Post::postDel($request, $id);
    }

    public function editPost(EditAdminPostRequest $request, $id)
    {
        return Post::editPost($request, $id);
    }

    // пользователи

    public function getUsers(Request $request)
    {
        return User::getUsers($request);
    }

    public function editUser(EditAdminUserRequest $request, $id)
    {
        return User::editUser($request, $id);
    }

    public function delUser(Request $request, $id)
    {
        return User::delUser($request, $id);
    }




}
