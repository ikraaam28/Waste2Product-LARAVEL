<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Get the default admin user ID
     */
    private function getDefaultAdminId()
    {
        return Auth::id() ?: User::where('email', 'admin@waste2product.com')->first()?->id ?: 1;
    }
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'creator']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'published':
                    $query->published();
                    break;
                case 'draft':
                    $query->where('is_active', false)->orWhereNull('published_at');
                    break;
            }
        }

        // Filter by stock status
        if ($request->has('stock_status') && !empty($request->stock_status)) {
            $query->where('stock_status', $request->stock_status);
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['name', 'price', 'stock_quantity', 'created_at', 'published_at', 'views_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::active()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'materials' => 'nullable|string|max:500',
            'recycling_process' => 'nullable|string|max:1000',
            'environmental_impact_score' => 'nullable|integer|min:1|max:100',
            'certifications' => 'nullable|array',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $productData = [
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'sku' => $request->sku ?: 'PRD-' . strtoupper(Str::random(8)),
            'stock_quantity' => $request->stock_quantity,
            'manage_stock' => $request->has('manage_stock'),
            'stock_status' => $request->stock_quantity > 0 ? 'in_stock' : 'out_of_stock',
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'materials' => $request->materials,
            'recycling_process' => $request->recycling_process,
            'environmental_impact_score' => $request->environmental_impact_score,
            'certifications' => $request->certifications,
            'tags' => $request->tags,
            'published_at' => $request->published_at ? $request->published_at : ($request->has('is_active') ? now() : null),
            'category_id' => $request->category_id,
            'created_by' => $this->getDefaultAdminId(),
        ];

        // Handle main images upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $productData['images'] = $images;
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('products/gallery', 'public');
                $gallery[] = $path;
            }
            $productData['gallery'] = $gallery;
        }

        Product::create($productData);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit créé avec succès !');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'creator']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'materials' => 'nullable|string|max:500',
            'recycling_process' => 'nullable|string|max:1000',
            'environmental_impact_score' => 'nullable|integer|min:1|max:100',
            'certifications' => 'nullable|array',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $productData = [
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'sku' => $request->sku,
            'stock_quantity' => $request->stock_quantity,
            'manage_stock' => $request->has('manage_stock'),
            'stock_status' => $request->stock_quantity > 0 ? 'in_stock' : 'out_of_stock',
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'materials' => $request->materials,
            'recycling_process' => $request->recycling_process,
            'environmental_impact_score' => $request->environmental_impact_score,
            'certifications' => $request->certifications,
            'tags' => $request->tags,
            'published_at' => $request->published_at,
            'category_id' => $request->category_id,
        ];

        // Handle main images upload
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $productData['images'] = $images;
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if ($product->gallery) {
                foreach ($product->gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('products/gallery', 'public');
                $gallery[] = $path;
            }
            $productData['gallery'] = $gallery;
        }

        $product->update($productData);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour avec succès !');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        if ($product->gallery) {
            foreach ($product->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès !');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active,
            'published_at' => !$product->is_active ? now() : $product->published_at
        ]);

        $status = $product->is_active ? 'activé' : 'désactivé';
        return redirect()->back()
            ->with('success', "Produit {$status} avec succès !");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Product $product)
    {
        $product->update([
            'is_featured' => !$product->is_featured
        ]);

        $status = $product->is_featured ? 'mis en vedette' : 'retiré de la vedette';
        return redirect()->back()
            ->with('success', "Produit {$status} avec succès !");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $products = Product::whereIn('id', $request->products);
        $count = $products->count();

        switch ($request->action) {
            case 'activate':
                $products->update(['is_active' => true, 'published_at' => now()]);
                $message = "{$count} produit(s) activé(s) avec succès !";
                break;

            case 'deactivate':
                $products->update(['is_active' => false]);
                $message = "{$count} produit(s) désactivé(s) avec succès !";
                break;

            case 'feature':
                $products->update(['is_featured' => true]);
                $message = "{$count} produit(s) mis en vedette avec succès !";
                break;

            case 'unfeature':
                $products->update(['is_featured' => false]);
                $message = "{$count} produit(s) retiré(s) de la vedette avec succès !";
                break;

            case 'delete':
                // Delete images for each product
                foreach ($products->get() as $product) {
                    if ($product->images) {
                        foreach ($product->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                    if ($product->gallery) {
                        foreach ($product->gallery as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }

                $products->delete();
                $message = "{$count} produit(s) supprimé(s) avec succès !";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export products to CSV
     */
    public function export(Request $request)
    {
        $query = Product::with(['category', 'creator']);

        // Apply same filters as index
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && !empty($request->status)) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
            }
        }

        $products = $query->get();

        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Nom', 'SKU', 'Catégorie', 'Prix', 'Stock', 'Statut Stock',
                'Actif', 'En Vedette', 'Créé par', 'Date de Création'
            ]);

            // CSV data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category->name,
                    $product->price,
                    $product->stock_quantity,
                    $product->stock_status_label,
                    $product->is_active ? 'Oui' : 'Non',
                    $product->is_featured ? 'Oui' : 'Non',
                    $product->creator->first_name . ' ' . $product->creator->last_name,
                    $product->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Duplicate a product
     */
    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copie)';
        $newProduct->slug = Str::slug($newProduct->name);
        $newProduct->sku = 'PRD-' . strtoupper(Str::random(8));
        $newProduct->is_active = false;
        $newProduct->published_at = null;
        $newProduct->views_count = 0;
        $newProduct->rating_average = 0;
        $newProduct->rating_count = 0;
        $newProduct->created_by = $this->getDefaultAdminId();
        $newProduct->save();

        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', 'Produit dupliqué avec succès ! Vous pouvez maintenant le modifier.');
    }
}
