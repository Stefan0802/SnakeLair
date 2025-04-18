<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Database;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditPasswordRequest;
use App\Http\Requests\UpdAvatarRequest;
use App\Http\Requests\EditUserRequest;

class ProfileController extends Controller
{
    public function profile(Request $request, $id)
    {
        return User::profile($request, $id);
    }

    public function editPassword(EditPasswordRequest $request)
    {
        return User::editPassword($request);
    }

    public function editorProfile(EditUserRequest $request)
    {
        return User::editorProfile($request);
    }

    public function uploadAvatar(UpdAvatarRequest $request)
    {
        return User::uploadAvatar($request);
    }

}
