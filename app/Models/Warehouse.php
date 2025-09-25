<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    // Champs remplissables
    protected $fillable = [
        'name',
        'partner_id',
        'location',
        'address',
        'city',
        'postal_code',
        'country',
        'capacity',
        'current_occupancy',
        'contact_person',
        'contact_phone',
        'contact_email',
        'status',
        'description'
    ];

    // Relation avec le partenaire
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }


    // Accesseurs
    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->current_occupancy;
    }

    public function getOccupancyPercentageAttribute()
    {
        if ($this->capacity > 0) {
            return round(($this->current_occupancy / $this->capacity) * 100, 2);
        }
        return 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    public function scopeWithAvailableCapacity($query)
    {
        return $query->whereRaw('current_occupancy < capacity');
    }
}