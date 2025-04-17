<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Database;
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
    public function profile(Request $request, $id = null)
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'Пользователь не авторизован'], 401);
        }

        $userId = $id ?? $loggedInUser ->id;

        // Убедитесь, что вы загружаете правильные отношения
        $user = User::with(['sentFriend', 'receivedFriend', 'post'])->find($userId);

        if ($user) {


            $response = [
                'status' => 'success',
                'user' => $user,
            ];
            return response()->json($response, 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Пользователь не найден'], 404);
    }








    public function editPassword(EditPasswordRequest $request)
    {
        $loggedInUser = Auth::user();

        if (!$loggedInUser) {
            $response = ['status' => 'error', 'message' => 'Пользователь не авторизован'];
            return response()->json($response, 401);
        }

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user) {

                if ($request->has('password')) {
                    $user->password = Hash::make($request->password);
                }

        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Пароль изменен',
            'user' => $user
        ]);
    }

    public function editorProfile(EditUserRequest $request)
    {

        $loggedInUser = Auth::user();

        if (!$loggedInUser) {
            $response = ['status' => 'error', 'message' => 'Пользователь не авторизован'];
            return response()->json($response, 401);
        }

        $user = User::where('email', $loggedInUser->email)->first();

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Данные изменены',
            'user' => $user
        ], 200);

    }


    public function uploadAvatar(UpdAvatarRequest $request)
    {
        $loggedInUser = Auth::user();

        if (!$loggedInUser) {
            $response = ['status' => 'error', 'message' => 'Пользователь не авторизован'];
            return response()->json($response, 401);
        }

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user) {


                $avatar = time() . '.' . $request->avatar->getClientOriginalExtension();

                $user->avatar = $request->avatar->move(public_path('uploads/images'),$avatar);

                $user->save();

                $response = ['status' => 'success'];

                return response()->json($response, 200);

        }

        $response = ['status' => 'error', 'message' => 'Пользователь не найден'];
        return response()->json($response, 404);
    }
}
