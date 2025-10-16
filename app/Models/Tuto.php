<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tuto extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'steps',
        'media',
        'user_id',
        'views',
        'is_published',
        'admin_notes',
    ];

    protected $casts = [
        'steps' => 'array',
        'media' => 'array',
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('created_at', 'desc');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function likes()
    {
        return $this->hasMany(Reaction::class)->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->hasMany(Reaction::class)->where('type', 'dislike');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    
}