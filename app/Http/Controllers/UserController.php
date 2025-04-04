<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|unique:users,email|email|max:255',
            'password' => 'required|min:6'
        ]);


        $user = User::create([
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        $data['token'] = $user->createToken($user->email)->plainTextToken;


        $response = [
            'status' => 'success',
            'message' => 'Пользователь создан',
            'data' => $data,
            'user' => $user
        ];

        return response()->json($response, 201);
    }

    public function login(LoginRequest $request)
    {


        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Неверные данные'
            ], 401);
        }


        $token = $user->createToken($request->email)->plainTextToken;


        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь успешно авторизироавн',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ], 200);
    }


    public function logout(Request $request)
    {

        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Пользователь не авторизован'
            ], 401);
        }


        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь успешно вышел'
        ], 200);
    }



}
