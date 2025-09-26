<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $table = 'tuto_user_reactions'; // table utilisée

    protected $fillable = [
        'user_id',
        'tuto_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tuto()
    {
        return $this->belongsTo(Tuto::class);
    }
}
