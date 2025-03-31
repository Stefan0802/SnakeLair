<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    /**
     * Связь с моделью User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с моделью Post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
