<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFeedback extends Model
{
    use HasFactory;

    protected $table = 'event_feedbacks';

    protected $fillable = [
        'event_id',
        'user_id',
        'rating',
        'comment',
        'photo',
        'recycled_quantity',
        'co2_saved',
        'satisfaction_level'
    ];

    protected $casts = [
        'rating' => 'integer',
        'recycled_quantity' => 'decimal:2',
        'co2_saved' => 'decimal:2',
        'satisfaction_level' => 'integer',
    ];

    /**
     * Relation avec l'événement
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
