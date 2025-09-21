<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\PublicationController;

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
});

Route::post('/profile/picture', [AuthController::class, 'updateProfilePicture'])->name('profile.picture.update');


// Routes des pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/testimonial', [PageController::class, 'testimonial'])->name('testimonial');
Route::get('/feature', [PageController::class, 'feature'])->name('feature');
// Public Events page placeholder
Route::view('/events', 'pages.events')->name('events');

// Routes des produits
Route::get('/products', [ProductController::class, 'index'])->name('products');

// Routes de la boutique
Route::get('/store', [StoreController::class, 'index'])->name('store');

// Routes du blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog');


// Routes pour publications
Route::middleware('auth')->group(function () {
    // Redéfinir la route pour /publications vers myPublications
    Route::get('/publications', [PublicationController::class, 'myPublications'])->name('publications.my');
    Route::post('publications/{publication}/commentaires', [CommentaireController::class, 'store'])->name('commentaires.store');

    // Garder resource pour les autres méthodes (store, show, etc.) si nécessaire
    Route::resource('publications', PublicationController::class)->except(['index']);
});
// Admin
Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
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
    Route::view('/events', 'admin.events.index')->name('admin.events.index');
    Route::view('/events/drop', 'admin.events.drop')->name('admin.events.drop');
    Route::view('/events/feedback', 'admin.events.feedback')->name('admin.events.feedback');
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
