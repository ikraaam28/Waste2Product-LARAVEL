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
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:on,1,true'
        ]);

        if ($validator->fails()) {
            \Log::error('Category validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $categoryData = [
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color ?: '#007bff',
            'sort_order' => $request->sort_order ?: 0,
            'is_active' => $request->has('is_active'),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $categoryData['image'] = $path;
        }

        $category = Category::create($categoryData);

        \Log::info('Category created successfully:', ['id' => $category->id, 'name' => $category->name]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès !');
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
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $categoryData = [
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color ?: '#007bff',
            'sort_order' => $request->sort_order ?: 0,
            'is_active' => $request->has('is_active'),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $categoryData['image'] = $path;
        }

        $category->update($categoryData);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès !');
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
