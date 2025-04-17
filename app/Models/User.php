<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'avatar',
        'role',
        'password',
    ];


    protected $hidden = [
        'password',
    ];


    public function isAdmin()
    {
        if (Auth::user()->role == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    public function sentFriend()
    {
        return $this->hasMany(Friend::class, 'user_id');
    }

    public function receivedFriend()
    {
        return $this->hasMany(Friend::class, 'friend_id');
    }
}

