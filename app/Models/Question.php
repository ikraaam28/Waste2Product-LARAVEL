<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tuto_id',
        'question_text',
        'parent_id',
    ];

    public function tuto()
    {
        return $this->belongsTo(Tuto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Question::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Question::class, 'parent_id');
    }
}