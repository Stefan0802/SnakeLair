<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Database;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function profile(Request $request, $id = null)
    {

        $loggedInUser   = $request->user();


        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }


        $userId = $id ?? $loggedInUser ->id;


        $user = User::with(['friends', 'friendOf', 'post'])->find($userId);


        if ($user) {
            $response = [
                'status' => 'success',
                'user' => $user,

            ];
            return response()->json($response, 200);
        }

        return response()->json(['status' => 'error', 'message' => 'User  not found'], 404);
    }


    public function editPassword(Request $request)
    {
        $loggedInUser = Auth::user();

        if (!$loggedInUser) {
            $response = ['status' => 'error', 'message' => 'User not authenticated'];
            return response()->json($response, 401);
        }

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user) {

            $validated = $request->validate([
                'password' => 'required|string|max:255',
                'confPassword' => 'required|string|max:255|same:password'
            ]);

            if ($validated){
                if ($request->has('password')) {
                    $user->password = Hash::make($request->password);
                }
            }
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'password changed',
            'user' => $user
        ]);
    }

    public function editorProfile(Request $request)
    {

        $loggedInUser = Auth::user();

        if (!$loggedInUser) {
            $response = ['status' => 'error', 'message' => 'User not authenticated'];
            return response()->json($response, 401);
        }

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user){

            $validated = $request->validate([
                'firstName' => 'sometimes|required|string|max:255',
                'lastName' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            if ($validated){
                if ($request->has('firstName')) {
                    $user->firstName = $request->firstName;
                }
                if ($request->has('lastName')) {
                    $user->lastName = $request->lastName;
                }
                if ($request->has('email')) {
                    $user->email = $request->email;
                }
            }
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'change succeeded',
            'user' => $user
        ], 200);

    }


    public function uploadAvatar(Request $request)
    {
        $loggedInUser = Auth::user();

        if (!$loggedInUser) {
            $response = ['status' => 'error', 'message' => 'User not authenticated'];
            return response()->json($response, 401);
        }

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user) {

            $validated = $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            if ($validated) {

                $avatar = time() . '.' . $request->avatar->getClientOriginalExtension();

                $user->avatar = $request->avatar->move(public_path('uploads/images'),$avatar);

                $user->save();

                $response = ['status' => 'success'];

                return response()->json($response, 200);
            }
        }

        $response = ['status' => 'error', 'message' => 'User not found'];
        return response()->json($response, 404);
    }
}
