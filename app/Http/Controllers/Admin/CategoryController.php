<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'sort_order');
        $sortOrder = $request->get('order', 'asc');
        
        $allowedSorts = ['name', 'sort_order', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $categories = $query->withCount('products')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        // Debug: Log all request data
        \Log::info('Category creation attempt:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100|regex:/^fa[srb]?\s+fa-[\w-]+$/',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|in:on,1,true'
        ], [
            // Custom error messages
            'name.required' => 'Category name is required.',
            'name.min' => 'Category name must be at least 2 characters long.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'name.unique' => 'A category with this name already exists.',
            'slug.max' => 'Slug cannot exceed 255 characters.',
            'slug.unique' => 'This URL slug is already taken. Please choose a different one.',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'image.image' => 'The uploaded file must be a valid image.',
            'image.mimes' => 'Image must be in JPG, JPEG, PNG, GIF, or WEBP format.',
            'image.max' => 'Image size must not exceed 2MB.',
            'icon.max' => 'Icon class cannot exceed 100 characters.',
            'icon.regex' => 'Please use a valid Font Awesome icon class (e.g., fas fa-tag).',
            'color.regex' => 'Please use a valid hex color format (e.g., #007bff).',
            'sort_order.integer' => 'Sort order must be a whole number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
        ]);

        if ($validator->fails()) {
            \Log::error('Category validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {
            // Validate and process icon
            $icon = $request->icon ?: 'fas fa-tag';
            if ($request->icon && !preg_match('/^fa[srb]?\s+fa-[\w-]+$/', $request->icon)) {
                $icon = 'fas fa-tag'; // Fallback to default if invalid
            }

            // Validate and process color
            $color = $request->color ?: '#007bff';
            if ($request->color && !preg_match('/^#[0-9A-Fa-f]{6}$/', $request->color)) {
                $color = '#007bff'; // Fallback to default if invalid
            }

            $categoryData = [
                'name' => trim($request->name),
                'slug' => $request->slug ?: Str::slug($request->name),
                'description' => $request->description ? trim($request->description) : null,
                'icon' => $icon,
                'color' => $color,
                'sort_order' => $request->sort_order ?: 0,
                'is_active' => $request->has('is_active'),
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Additional server-side validation
                if ($image->getSize() > 2097152) { // 2MB
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Image size must not exceed 2MB.');
                }

                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Please upload a valid image file (JPG, PNG, GIF, WEBP).');
                }

                $path = $image->store('categories', 'public');
                $categoryData['image'] = $path;
            }

            $category = Category::create($categoryData);

            \Log::info('Category created successfully:', ['id' => $category->id, 'name' => $category->name]);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            \Log::error('Error creating category: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the category. Please try again.');
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(\Illuminate\Http\Request $request, \App\Models\Category $category)
    {
        \Log::debug('Category update attempt', ['id' => $category->id, 'payload' => $request->all()]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|size:7', // #rrggbb
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048', // 2MB
        ]);

        // checkbox: convertir en bool
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // générer slug si vide
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // traiter l'image si fournie
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $path = $request->file('image')->store('categories', 'public');
                // supprimer ancienne image si besoin (optionnel)
                if ($category->image) {
                    try {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to delete old category image', ['err' => $e->getMessage()]);
                    }
                }
                $data['image'] = $path;
            } catch (\Throwable $e) {
                \Log::error('Category image upload failed: '.$e->getMessage());
                return back()->withErrors(['image' => 'Erreur lors de l\'upload de l\'image'])->withInput();
            }
        }

        try {
            $category->update($data);
            \Log::info('Category updated', ['id' => $category->id]);
            return redirect()->route('admin.categories.index')->with('success', 'Category mise à jour');
        } catch (\Throwable $e) {
            \Log::error('Category update failed: '.$e->getMessage(), ['id' => $category->id, 'data' => $data]);
            return back()->withErrors(['general' => 'Impossible de mettre à jour la catégorie'])->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès !');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        $status = $category->is_active ? 'activée' : 'désactivée';
        return redirect()->back()
            ->with('success', "Catégorie {$status} avec succès !");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $categories = Category::whereIn('id', $request->categories);
        $count = $categories->count();

        switch ($request->action) {
            case 'activate':
                $categories->update(['is_active' => true]);
                $message = "{$count} catégorie(s) activée(s) avec succès !";
                break;
            
            case 'deactivate':
                $categories->update(['is_active' => false]);
                $message = "{$count} catégorie(s) désactivée(s) avec succès !";
                break;
            
            case 'delete':
                // Check if any category has products
                $categoriesWithProducts = $categories->has('products')->count();
                if ($categoriesWithProducts > 0) {
                    return redirect()->back()
                        ->with('error', 'Impossible de supprimer les catégories qui contiennent des produits.');
                }
                
                // Delete images
                foreach ($categories->get() as $category) {
                    if ($category->image) {
                        Storage::disk('public')->delete($category->image);
                    }
                }
                
                $categories->delete();
                $message = "{$count} catégorie(s) supprimée(s) avec succès !";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get categories for API/AJAX
     */
    public function getCategories(Request $request)
    {
        $categories = Category::active()
            ->ordered()
            ->select('id', 'name', 'slug', 'color', 'icon')
            ->get();

        return response()->json($categories);
    }
}
