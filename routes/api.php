<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\PostsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::controller(UserController::class)->group(function (){
//    Route::post('register', 'register'); // регистрация
//});


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->middleware('auth:sanctum');
Route::post('/profile/editor', [ProfileController::class, 'editorProfile'])->middleware('auth:sanctum');
Route::post('/profile/password', [ProfileController::class, 'editPassword'])->middleware('auth:sanctum');

Route::get('/profile/{id?}', [ProfileController::class, 'profile'])->middleware('auth:sanctum');

Route::get('/friends', [FriendController::class, 'index'])->middleware('auth:sanctum');
Route::post('/friends/add/{friendId}', [FriendController::class, 'addFriend'])->middleware('auth:sanctum');

Route::get('/posts', [PostsController::class, 'posts'])->middleware('auth:sanctum');

