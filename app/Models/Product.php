<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'sku',
        'stock_quantity',
        'manage_stock',
        'stock_status',
        'is_featured',
        'is_active',
        'images',
        'gallery',
        'weight',
        'dimensions',
        'attributes',
        'materials',
        'recycling_process',
        'environmental_impact_score',
        'certifications',
        'tags',
        'meta_data',
        'views_count',
        'rating_average',
        'rating_count',
        'published_at',
        'category_id',
        'created_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'manage_stock' => 'boolean',
        'images' => 'array',
        'gallery' => 'array',
        'attributes' => 'array',
        'certifications' => 'array',
        'tags' => 'array',
        'meta_data' => 'array',
        'published_at' => 'datetime',
        'rating_average' => 'decimal:2',
        'stock_quantity' => 'integer',
        'views_count' => 'integer',
        'rating_count' => 'integer',
        'environmental_impact_score' => 'integer'
    ];

    /**
     * Boot method pour générer automatiquement le slug et SKU
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec les événements
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_products');
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les produits publiés
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope pour les produits en vedette
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope pour les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    /**
     * Scope pour recherche
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('short_description', 'LIKE', "%{$term}%")
              ->orWhere('sku', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Obtenir l'image principale
     */
    public function getMainImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            $imagePath = $this->images[0];
            // Vérifier si le fichier existe dans storage/app/public
            if (file_exists(storage_path('app/public/' . $imagePath))) {
                return asset('storage/' . $imagePath);
            }
        }
        return asset('assets/img/product-placeholder.svg');
    }

    /**
     * Obtenir l'URL de la première image
     */
    public function getFirstImageUrlAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            $imagePath = $this->images[0];

            // Vérifier si le fichier existe dans public/storage (lien symbolique)
            if (file_exists(public_path('storage/' . $imagePath))) {
                return asset('storage/' . $imagePath);
            }

            // Vérifier si le fichier existe dans storage/app/public
            if (file_exists(storage_path('app/public/' . $imagePath))) {
                // Copier le fichier vers public/storage si nécessaire
                $publicStoragePath = public_path('storage/' . dirname($imagePath));
                if (!is_dir($publicStoragePath)) {
                    mkdir($publicStoragePath, 0755, true);
                }

                $sourceFile = storage_path('app/public/' . $imagePath);
                $destFile = public_path('storage/' . $imagePath);

                if (!file_exists($destFile)) {
                    copy($sourceFile, $destFile);
                }

                return asset('storage/' . $imagePath);
            }
        }
        return asset('assets/img/product-placeholder.svg');
    }

    /**
     * Obtenir l'URL de l'image avec taille spécifiée
     */
    public function getImageUrl($width = 300, $height = 200)
    {
        $imageUrl = $this->first_image_url;

        // Si c'est le placeholder, le retourner tel quel
        if (str_contains($imageUrl, 'product-placeholder.svg')) {
            return $imageUrl;
        }

        return $imageUrl;
    }

    /**
     * Obtenir toutes les images
     */
    public function getAllImagesAttribute()
    {
        $images = [];
        if ($this->images) {
            foreach ($this->images as $image) {
                $images[] = asset('storage/' . $image);
            }
        }
        if ($this->gallery) {
            foreach ($this->gallery as $image) {
                $images[] = asset('storage/' . $image);
            }
        }
        return $images;
    }

    /**
     * Vérifier si le produit est en stock
     */
    public function isInStock()
    {
        if (!$this->manage_stock) {
            return true;
        }
        return $this->stock_quantity > 0 && $this->stock_status === 'in_stock';
    }

    /**
     * Obtenir le statut du stock avec label
     */
    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'in_stock' => 'En stock',
            'out_of_stock' => 'Rupture de stock',
            'on_backorder' => 'En commande',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir la couleur du badge de statut
     */
    public function getStockStatusColorAttribute()
    {
        return match($this->stock_status) {
            'in_stock' => 'success',
            'out_of_stock' => 'danger',
            'on_backorder' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Calculer le pourcentage de réduction
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    /**
     * Obtenir le score environnemental avec label
     */
    public function getEnvironmentalScoreLabelAttribute()
    {
        if (!$this->environmental_impact_score) {
            return 'Non évalué';
        }

        return match(true) {
            $this->environmental_impact_score >= 80 => 'Excellent',
            $this->environmental_impact_score >= 60 => 'Très bon',
            $this->environmental_impact_score >= 40 => 'Bon',
            $this->environmental_impact_score >= 20 => 'Moyen',
            default => 'Faible'
        };
    }

    /**
     * Obtenir la couleur du score environnemental
     */
    public function getEnvironmentalScoreColorAttribute()
    {
        if (!$this->environmental_impact_score) {
            return 'secondary';
        }

        return match(true) {
            $this->environmental_impact_score >= 80 => 'success',
            $this->environmental_impact_score >= 60 => 'info',
            $this->environmental_impact_score >= 40 => 'primary',
            $this->environmental_impact_score >= 20 => 'warning',
            default => 'danger'
        };
    }





    /**
     * Incrémenter le nombre de vues
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Route key name pour les URLs
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
