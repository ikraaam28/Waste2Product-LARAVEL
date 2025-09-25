<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'meta_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta_data' => 'array',
        'sort_order' => 'integer'
    ];

    /**
     * Boot method pour générer automatiquement le slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relation avec les produits
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope pour les catégories actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour trier par ordre
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Obtenir le nombre de produits actifs
     */
    public function getActiveProductsCountAttribute()
    {
        return $this->products()->where('is_active', true)->count();
    }

    /**
     * Obtenir l'URL de l'image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-category.png');
    }

    /**
     * Obtenir la classe CSS pour l'icône
     */
    public function getIconClassAttribute()
    {
        return $this->icon ?: 'fas fa-tag';
    }

    /**
     * Vérifier si la catégorie a des produits
     */
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    /**
     * Obtenir les statistiques de la catégorie
     */
    public function getStatsAttribute()
    {
        return [
            'total_products' => $this->products()->count(),
            'active_products' => $this->products()->where('is_active', true)->count(),
            'featured_products' => $this->products()->where('is_featured', true)->count(),
            'out_of_stock' => $this->products()->where('stock_status', 'out_of_stock')->count(),
        ];
    }

    /**
     * Route key name pour les URLs
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
