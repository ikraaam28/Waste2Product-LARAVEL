<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'date',
        'time',
        'location',
        'city',
        'image',
        'status',
        'max_participants',
        'qr_code',
        'created_by',
        'organizer_email'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * Relation avec les participants
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_participants')
                    ->withPivot(['scanned_at', 'badge_earned'])
                    ->withTimestamps();
    }

    /**
     * Relation avec les produits liés
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'event_products');
    }

    /**
     * Relation avec les feedbacks
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(EventFeedback::class);
    }

    /**
     * Relation avec le créateur
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les événements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope pour les événements passés
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now());
    }

    /**
     * Scope pour les événements futurs
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now());
    }

    /**
     * Obtenir le nombre de participants scannés
     */
    public function getScannedParticipantsCountAttribute(): int
    {
        return $this->participants()->whereNotNull('event_participants.scanned_at')->count();
    }

    /**
     * Obtenir le nombre total de participants
     */
    public function getTotalParticipantsCountAttribute(): int
    {
        return $this->participants()->count();
    }
}
