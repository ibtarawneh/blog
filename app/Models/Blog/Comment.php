<?php
// app/Models/Blog/Comment.php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import the User model

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'blog_id', 'text', 'like', 'dislike',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class); // Reference the User model
    }
}
