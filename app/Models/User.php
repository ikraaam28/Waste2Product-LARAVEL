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
        'avatar'
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
 * Vérifier si l'utilisateur est un administrateur.
 */
public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
}
