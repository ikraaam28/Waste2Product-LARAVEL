<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',      // Titre de la publication
        'contenu',    // Description/text
        'categorie',  // Catégorie: 'reemploi', 'reparation', 'transformation'
        'image',      // Chemin vers photo/vidéo (optionnel)
        'user_id',    // ID de l'utilisateur qui poste
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }
}