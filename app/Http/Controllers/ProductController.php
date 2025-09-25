<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer toutes les catégories actives avec leurs produits actifs et publiés
        $categories = Category::active()
            ->ordered()
            ->with(['products' => function($query) {
                $query->active()
                      ->published()
                      ->orderBy('is_featured', 'desc')
                      ->orderBy('created_at', 'desc');
            }])
            ->get();

        // Filtrer les catégories qui ont au moins un produit
        $categories = $categories->filter(function($category) {
            return $category->products->count() > 0;
        });

        // Si une catégorie spécifique est demandée
        $selectedCategory = null;
        if ($request->has('category')) {
            $selectedCategory = Category::where('slug', $request->category)
                ->active()
                ->first();

            if ($selectedCategory) {
                $categories = collect([$selectedCategory->load(['products' => function($query) {
                    $query->active()
                          ->published()
                          ->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc');
                }])]);
            }
        }

        return view('products.index', compact('categories', 'selectedCategory'));
    }

    /**
     * Afficher les produits d'une catégorie spécifique
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->active()
            ->published()
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('products.category', compact('category', 'products'));
    }

    /**
     * Afficher un produit spécifique
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->published()
            ->with(['category'])
            ->firstOrFail();

        // Incrémenter le compteur de vues
        $product->increment('views_count');

        // Produits similaires de la même catégorie
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->published()
            ->orderBy('is_featured', 'desc')
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
