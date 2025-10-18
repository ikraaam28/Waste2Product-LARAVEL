<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'contenu',
        'publication_id',
        'user_id',
        'parent_id', // Pour les réponses (optionnel)
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec la publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les réponses (commentaires enfants)
     */
    public function replies()
    {
        return $this->hasMany(Commentaire::class, 'parent_id');
    }

    /**
     * Relation avec le commentaire parent
     */
    public function parent()
    {
        return $this->belongsTo(Commentaire::class, 'parent_id');
    }

    /**
     * Vérifier si c'est une réponse
     */
    public function isReply()
    {
        return $this->parent_id !== null;
    }
}