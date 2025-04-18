<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;



class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return User::register($request);
    }

    public function login(LoginRequest $request)
    {
        return User::login($request);
    }

    public function logout(Request $request)
    {
        return User::logout($request);
    }

}
