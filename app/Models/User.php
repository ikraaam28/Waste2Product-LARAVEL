<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    /**
     * Les attributs qui peuvent être remplis en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'role',
        'is_active',
        'phone',
        'city',
        'company_name',
        'company_description',
        'business_license',
        'supplier_categories',
        'password',
        'newsletter_subscription',
        'terms_accepted',
        'email_verified_at',
        'remember_token',
        'profile_picture',
        'google_id',
        'avatar',
        'banned_at',
        'ban_reason',
        'banned_until',
    ];

    /**
     * Les attributs cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'newsletter_subscription' => 'boolean',
        'terms_accepted' => 'boolean',
        'is_active' => 'boolean',
        'supplier_categories' => 'array',
    ];

    /**
     * Obtenir le nom complet de l'utilisateur.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Relation avec les badges
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot(['earned_at', 'event_id'])
                    ->withTimestamps();
    }



    /**
     * Relation avec les événements participés
     */
    public function participatedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participants')
                    ->withPivot(['participant_id', 'scanned_at', 'badge_earned'])
                    ->withTimestamps();
    }

    /**
     * Vérifier si l'utilisateur est un administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifier si l'utilisateur est un utilisateur ordinaire.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Vérifier si l'utilisateur est un fournisseur.
     */
    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    /**
     * Vérifier si l'utilisateur est actif.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Obtenir le libellé du rôle.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrateur',
            'user' => 'Utilisateur',
            'supplier' => 'Fournisseur',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir la couleur du badge pour le rôle.
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'danger',
            'user' => 'primary',
            'supplier' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Obtenir l'icône du rôle.
     */
    public function getRoleIconAttribute(): string
    {
        return match($this->role) {
            'admin' => 'fas fa-crown',
            'user' => 'fas fa-user',
            'supplier' => 'fas fa-store',
            default => 'fas fa-question'
        };
    }

    /**
     * Scope pour filtrer par rôle.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope pour les utilisateurs actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les utilisateurs inactifs.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

protected $dates = ['banned_at'];

    public function isBanned()
    {
        return !is_null($this->banned_at);
    }


    /**
     * Publication et commentaire
     */
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function publicationReactions()
    {
        return $this->hasMany(PublicationReaction::class);
    }

    /**
     * Get all publications liked by this user
     */
    public function likedPublications()
    {
        return $this->hasManyThrough(
            Publication::class,
            PublicationReaction::class,
            'user_id', // Foreign key on reactions table
            'id',      // Local key on publications table
            'id',      // Local key on users table
            'publication_id' // Foreign key on reactions table
        )->whereHas('publicationReactions', function ($query) {
            $query->where('type', 'like');
        });
    }

    /**
     * Get all publications disliked by this user
     */
    public function dislikedPublications()
    {
        return $this->hasManyThrough(
            Publication::class,
            PublicationReaction::class,
            'user_id',
            'id',
            'id',
            'publication_id'
        )->whereHas('publicationReactions', function ($query) {
            $query->where('type', 'dislike');
        });
    }
}
