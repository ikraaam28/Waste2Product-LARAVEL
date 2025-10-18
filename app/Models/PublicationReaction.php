<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationReaction extends Model
{
    use HasFactory;

    protected $table = 'publication_reactions';

    protected $fillable = [
        'user_id',
        'publication_id',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function scopeLikes($query)
    {
        return $query->where('type', 'like');
    }

    public function scopeDislikes($query)
    {
        return $query->where('type', 'dislike');
    }
}