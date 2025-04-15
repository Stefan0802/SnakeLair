<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;


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
Route::put('/profile/editor', [ProfileController::class, 'editorProfile'])->middleware('auth:sanctum');
Route::put('/profile/password', [ProfileController::class, 'editPassword'])->middleware('auth:sanctum');

Route::get('/profile/{id?}', [ProfileController::class, 'profile'])->middleware('auth:sanctum');

Route::get('/friends', [FriendController::class, 'index'])->middleware('auth:sanctum');
Route::post('/friends/add/{friendId}', [FriendController::class, 'addFriend'])->middleware('auth:sanctum');
Route::delete('/friends/del/{friendId}', [FriendController::class, 'removeFriend'])->middleware('auth:sanctum');

Route::get('/posts', [PostsController::class, 'posts'])->middleware('auth:sanctum');
Route::post('/posts/create', [PostsController::class, 'createPost'])->middleware('auth:sanctum');
Route::post('/posts/comments', [PostsController::class, 'addComment'])->middleware('auth:sanctum');
Route::post('/posts/like-toggle/{id}', [PostsController::class, 'toggleLike'])->middleware('auth:sanctum');
Route::delete('/posts/{id}', [PostsController::class, 'deletePost'])->middleware('auth:sanctum');
Route::put('/posts/{id}', [PostsController::class, 'updatePost'])->middleware('auth:sanctum');

Route::get('/chat', [ChatController::class, 'chat'])->middleware('auth:sanctum');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->middleware('auth:sanctum');
Route::get('/chat/{receiverId}/messages', [ChatController::class, 'getMessages'])->middleware('auth:sanctum');

Route::get('admin/posts', [AdminController::class, 'posts'])->middleware('auth:sanctum');
Route::delete('admin/posts/{id}', [AdminController::class, 'postDel'])->middleware('auth:sanctum');
Route::put('admin/posts/{id}', [AdminController::class, 'editPost'])->middleware('auth:sanctum');

Route::get('admin/users', [AdminController::class, 'getUsers'])->middleware('auth:sanctum');
Route::put( 'admin/users/{id}', [AdminController::class, 'editUser'])->middleware('auth:sanctum');
Route::delete( 'admin/users/{id}', [AdminController::class, 'delUser'])->middleware( 'auth:sanctum');
