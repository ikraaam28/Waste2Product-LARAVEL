<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    // Champs remplissables
    protected $fillable = [
        'name',
        'email',
        'phone',
        'type',
        'address',
    ];

    // Relations (si plus tard tu ajoutes entrepôts)
     public function warehouses()
     {
         return $this->hasMany(Warehouse::class);
     }
}
