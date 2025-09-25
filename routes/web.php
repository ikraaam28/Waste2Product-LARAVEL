<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\WarehouseController;

// Routes principales
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes d'authentification
Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/signup', [AuthController::class, 'store'])->name('signup.store');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Google OAuth routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('my-events');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/reset-password', [AuthController::class, 'redirectToResetPassword'])->name('profile.reset-password');
});

Route::post('/profile/picture', [AuthController::class, 'updateProfilePicture'])->name('profile.picture.update');

// Routes des pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/testimonial', [PageController::class, 'testimonial'])->name('testimonial');
Route::get('/feature', [PageController::class, 'feature'])->name('feature');

// Public Events routes
Route::get('/events', [EventController::class, 'publicIndex'])->name('events');
Route::get('/events/{event}', [EventController::class, 'publicShow'])->name('events.show');
Route::post('/events/{event}/participate', [EventController::class, 'participate'])->name('events.participate');
Route::get('/events/{event}/qr/{participant}', [EventController::class, 'showQrCode'])->name('events.qr');
Route::post('/events/{event}/feedback', [EventController::class, 'storeFeedback'])->name('events.feedback.store');

// Frontend list of partners with filters
Route::get('/partners', [PartnerController::class, 'front'])->name('partners.front');

// Frontend single partner details
Route::get('/partners/{partner}', [PartnerController::class, 'showFront'])->name('partners.show');

// Routes frontend si nécessaire
Route::get('warehouses', [WarehouseController::class, 'frontIndex'])->name('warehouses.front');
Route::get('warehouses/{warehouse}', [WarehouseController::class, 'frontShow'])->name('warehouses.show');

// Routes des produits
Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products');
Route::get('/products/category/{slug}', [App\Http\Controllers\Admin\ProductController::class, 'category'])->name('products.category');
Route::get('/products/{slug}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');

// Routes de la boutique
Route::get('/store', [StoreController::class, 'index'])->name('store');

// Routes du blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog');

// Admin
Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');

// Test route for debugging
Route::get('/admin/categories/test', function() {
    return 'Categories index test - this should work';
});

// Test route for simple category creation
Route::get('/admin/categories/test-create', function() {
    return view('admin.categories.test-create');
})->name('admin.categories.test-create');

// Test route for debugging images
Route::get('/test-images', function() {
    $product = App\Models\Product::first();
    if (!$product) {
        return 'Aucun produit trouvé';
    }

    $debug = [
        'product_id' => $product->id,
        'product_name' => $product->name,
        'images_array' => $product->images,
        'first_image_path' => $product->images[0] ?? 'Aucune image',
        'storage_path' => storage_path('app/public/' . ($product->images[0] ?? '')),
        'file_exists' => file_exists(storage_path('app/public/' . ($product->images[0] ?? ''))),
        'public_path' => public_path('storage/' . ($product->images[0] ?? '')),
        'public_file_exists' => file_exists(public_path('storage/' . ($product->images[0] ?? ''))),
        'asset_url' => asset('storage/' . ($product->images[0] ?? '')),
        'first_image_url' => $product->first_image_url,
    ];

    return '<pre>' . print_r($debug, true) . '</pre>';
});

// Admin Management Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // User Management Routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-verification', [App\Http\Controllers\Admin\UserController::class, 'toggleVerification'])->name('users.toggle-verification');
    Route::patch('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('users-export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');

    // Category Management Routes
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::patch('categories/{category}/toggle-status', [App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/bulk-action', [App\Http\Controllers\Admin\CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    Route::get('categories-api', [App\Http\Controllers\Admin\CategoryController::class, 'getCategories'])->name('categories.api');

    // Product Management Routes
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::patch('products/{product}/toggle-status', [App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [App\Http\Controllers\Admin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::post('products/bulk-action', [App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::get('products-export', [App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
    Route::post('products/{product}/duplicate', [App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
});
// Admin components
Route::prefix('admin')->group(function () {
    Route::view('/components/avatars', 'admin.components.avatars')->name('admin.components.avatars');
    Route::view('/components/buttons', 'admin.components.buttons')->name('admin.components.buttons');
    Route::view('/components/gridsystem', 'admin.components.gridsystem')->name('admin.components.gridsystem');
    Route::view('/components/panels', 'admin.components.panels')->name('admin.components.panels');
    Route::view('/components/notifications', 'admin.components.notifications')->name('admin.components.notifications');
    Route::view('/components/sweetalert', 'admin.components.sweetalert')->name('admin.components.sweetalert');
    Route::view('/components/font-awesome-icons', 'admin.components.font-awesome-icons')->name('admin.components.fontawesome');
    Route::view('/components/simple-line-icons', 'admin.components.simple-line-icons')->name('admin.components.simpleline');
    Route::view('/components/typography', 'admin.components.typography')->name('admin.components.typography');

    // Forms
    Route::view('/forms/forms', 'admin.forms.forms')->name('admin.forms.forms');

    // Tables
    Route::view('/tables/tables', 'admin.tables.tables')->name('admin.tables.tables');
    Route::view('/tables/datatables', 'admin.tables.datatables')->name('admin.tables.datatables');

    // Charts
    Route::view('/charts/charts', 'admin.charts.charts')->name('admin.charts.charts');
    Route::view('/charts/sparkline', 'admin.charts.sparkline')->name('admin.charts.sparkline');

    // Maps
    Route::view('/maps/googlemaps', 'admin.maps.googlemaps')->name('admin.maps.googlemaps');
    Route::view('/maps/jsvectormap', 'admin.maps.jsvectormap')->name('admin.maps.jsvectormap');

    // Widgets
    Route::view('/widgets', 'admin.widgets')->name('admin.widgets');

    // Events
    Route::get('/events/dashboard', [EventController::class, 'dashboard'])->name('admin.events.dashboard');
    Route::get('/events', [EventController::class, 'index'])->name('admin.events.index');
    Route::get('/events/manage', [EventController::class, 'manage'])->name('admin.events.manage');
    Route::get('/events/create', [EventController::class, 'create'])->name('admin.events.create');
    Route::post('/events', [EventController::class, 'store'])->name('admin.events.store');
    Route::get('/events/qr-scanner', [EventController::class, 'qrScanner'])->name('admin.events.qr-scanner');
    Route::post('/events/scan-qr', [EventController::class, 'scanQr'])->name('admin.events.scan-qr');
    Route::get('/events/feedback', [EventController::class, 'feedback'])->name('admin.events.feedback');
    Route::get('/events/badges', [EventController::class, 'badges'])->name('admin.events.badges');
    Route::post('/events/badges', [EventController::class, 'createBadge'])->name('admin.events.create-badge');
    Route::get('/events/api/events', [EventController::class, 'apiEvents'])->name('admin.events.api');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('admin.events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');
    Route::patch('/events/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('admin.events.toggle-status');

    // Partners Management
    Route::get('partners', [PartnerController::class, 'index'])->name('admin.partners.index');
    Route::get('partners/create', [PartnerController::class, 'create'])->name('admin.partners.create');
    Route::post('partners', [PartnerController::class, 'store'])->name('admin.partners.store');
    Route::get('partners/{partner}', [PartnerController::class, 'show'])->name('admin.partners.show');
    Route::get('partners/{partner}/edit', [PartnerController::class, 'edit'])->name('admin.partners.edit');
    Route::put('partners/{partner}', [PartnerController::class, 'update'])->name('admin.partners.update');
    Route::delete('partners/{partner}', [PartnerController::class, 'destroy'])->name('admin.partners.destroy');

    // Warehouses Management 
    Route::get('warehouses', [WarehouseController::class, 'index'])->name('admin.warehouses.index');
    Route::get('warehouses/create', [WarehouseController::class, 'create'])->name('admin.warehouses.create');
    Route::post('warehouses', [WarehouseController::class, 'store'])->name('admin.warehouses.store');
    Route::get('warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('admin.warehouses.show');
    Route::get('warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('admin.warehouses.edit');
    Route::put('warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('admin.warehouses.update');
    Route::delete('warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('admin.warehouses.destroy');
    Route::get('partners/{partner}/warehouses', [WarehouseController::class, 'getByPartner'])->name('admin.partners.warehouses');
});











// Removed catch-all to external template pages to avoid dependency on kaiadmin-lite

// Legacy redirects: map old /admin/pages/* URLs to new Blade routes
Route::redirect('/admin/pages', '/admin');
Route::redirect('/admin/pages/', '/admin');
Route::get('/admin/pages/{slug}', function (string $slug) {
    $slug = trim($slug, '/');
    if ($slug === '' || $slug === 'index' || $slug === 'index.html') {
        return redirect()->to(url('admin'));
    }

    // drop trailing .html if present
    if (substr($slug, -5) === '.html') {
        $slug = substr($slug, 0, -5);
    }

    // Normalize any leading ./ or ../ segments
    $slug = preg_replace('#^(?:\./)+#', '', $slug);
    $slug = preg_replace('#^(?:\.+/)+#', '', $slug);

    // Direct mappings for known sections
    $sections = ['components', 'charts', 'tables', 'maps'];
    foreach ($sections as $section) {
        if (strpos($slug, $section . '/') === 0) {
            $rest = substr($slug, strlen($section) + 1);
            return redirect()->to(url("admin/{$section}/{$rest}"));
        }
    }

    if ($slug === 'forms/forms') {
        return redirect()->to(url('admin/forms/forms'));
    }
    if ($slug === 'widgets') {
        return redirect()->to(url('admin/widgets'));
    }

    abort(404);
});