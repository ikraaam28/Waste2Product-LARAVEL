<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'criteria_type',
        'criteria_value',
        'points_required',
        'is_active'
    ];

    protected $casts = [
        'criteria_value' => 'integer',
        'points_required' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les utilisateurs qui ont obtenu ce badge
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot(['earned_at', 'event_id'])
                    ->withTimestamps();
    }

    /**
     * Scope pour les badges actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
