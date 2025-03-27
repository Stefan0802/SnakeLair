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

        // Создание пользователя
        $user = User::create([
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        $data['token'] = $user->createToken($user->email)->plainTextToken;


        $response = [
            'status' => 'success',
            'message' => 'User  is created',
            'data' => $data,
            'user' => $user
        ];

        return response()->json($response, 201);
    }

    public function login(LoginRequest $request)
    {

        // Поиск пользователя по email
        $user = User::where('email', $request->email)->first();

        // Проверка наличия пользователя и проверки пароля
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Создание токена
        $token = $user->createToken($request->email)->plainTextToken;

        // Формирование ответа
        return response()->json([
            'status' => 'success',
            'message' => 'User  is logged in successfully.',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ], 200);
    }


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User is logged'
        ], 200);
    }


}
