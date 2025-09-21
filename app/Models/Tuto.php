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
        'category',
        'steps',
        'media',
        'views',
        'likes_count',
        'dislikes_count',
        'user_id',
    ];

    protected $casts = [
        'steps' => 'array',
        'media' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}