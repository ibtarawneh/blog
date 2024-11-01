<?php
// app/Models/Blog/Blog.php
// app/Models/Blog/Blog.php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import the User model

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'content', 'image', 'views', 'likes',
    ];
    
    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
