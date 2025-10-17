<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'categorie',
        'image',
        'user_id',
    ];

    // Existing relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    // NEW: Reaction relationships
    public function publicationReactions()
    {
        return $this->hasMany(PublicationReaction::class);
    }

    public function likes()
    {
        return $this->hasMany(PublicationReaction::class)->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->hasMany(PublicationReaction::class)->where('type', 'dislike');
    }

    // Accessors for counts
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->dislikes()->count();
    }
    

    // Check user reactions
    public function isLikedByAuthUser()
    {
        return auth()->check() && $this->publicationReactions()
            ->where('type', 'like')
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function isDislikedByAuthUser()
    {
        return auth()->check() && $this->publicationReactions()
            ->where('type', 'dislike')
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function userReactionType()
    {
        if (!auth()->check()) {
            return null;
        }
        return $this->publicationReactions()
            ->where('user_id', auth()->id())
            ->first()?->type;
    }

    public static function boot()
    {
        parent::boot();

        // Automatically delete publications if the user is banned
        static::saving(function ($publication) {
            if ($publication->user && $publication->user->isBanned()) {
                $publication->delete();
                throw new \Exception('Cannot save publication: User is banned.');
            }
        });
    }
}