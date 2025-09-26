<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuto extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'steps',
        'media',
        'user_id',
        'is_published',
        'admin_notes',
        'views',
        'likes_count',
        'dislikes_count',
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

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
