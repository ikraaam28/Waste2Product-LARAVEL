<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\PublicationReactionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\TutoController;
use App\Http\Controllers\QuizController;
use GuzzleHttp\Client;


use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\APC;
use Prometheus\RenderTextFormat;

Route::get('/metrics', function() {
    // prefer APCu (persiste entre requêtes PHP-FPM) sinon fallback InMemory
    $adapter = null;
    if (extension_loaded('apcu') && class_exists('APCIterator')) {
        try {
            $adapter = new APC();
        } catch (\Throwable $e) {
            // APC adapter not usable (ex: class missing internals), fallback below
            $adapter = null;
        }
    }

    if ($adapter === null) {
        $adapter = new InMemory();
    }

    $registry = new CollectorRegistry($adapter);
    $renderer = new RenderTextFormat();

    // register safe (catch si déjà enregistré)
    try {
        $counter = $registry->registerCounter('app', 'requests_total', 'Total requests', ['method']);
    } catch (\Exception $e) {
        // metric probablement déjà enregistrée par une précédente requête
    }

    try {
        if (isset($counter)) {
            $counter->inc(['GET']);
        }
    } catch (\Throwable $e) {
        // ignore increment errors
    }

    $metrics = $registry->getMetricFamilySamples();

    if (empty($metrics)) {
        return response("# no metrics yet\n", 200)
            ->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }

    $result = $renderer->render($metrics);
    return response($result, 200)
        ->header('Content-Type', RenderTextFormat::MIME_TYPE);
});



// Routes principales
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes d'authentification
Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/signup', [AuthController::class, 'store'])->name('signup.store');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

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
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Routes de la boutique
Route::get('/store', [StoreController::class, 'index'])->name('store');

// Routes du blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog');


Route::post('/generate-title', [PublicationController::class, 'generateTitle'])->name('publications.generateTitle');
Route::get('/publications/{id}/translate', [PublicationController::class, 'translate']);


Route::get('/test-title', function() {
    return [
        'token' => !empty(env('HUGGINGFACE_TOKEN')) ? '✅ Present' : '❌ Missing',
        'content' => request('content'),
        'timestamp' => now()
    ];
});

// Routes pour publications
Route::middleware('auth')->group(function () {
    // Redéfinir la route pour /publications vers myPublications
    Route::get('/publications', [PublicationController::class, 'myPublications'])->name('publications.my');
    Route::post('publications/{publication}/commentaires', [CommentaireController::class, 'store'])->name('commentaires.store');

    // Garder resource pour les autres méthodes (store, show, etc.) si nécessaire
    Route::resource('publications', PublicationController::class)->except(['index']);
    // Routes pour les commentaires
    Route::post('publications/{publication}/commentaires', [CommentaireController::class, 'store'])
        ->name('commentaires.store');
    Route::get('commentaires/{commentaire}/edit', [CommentaireController::class, 'edit'])
        ->name('commentaires.edit');
    Route::put('commentaires/{commentaire}', [CommentaireController::class, 'update'])
        ->name('commentaires.update');
    Route::delete('commentaires/{commentaire}', [CommentaireController::class, 'destroy'])
        ->name('commentaires.destroy');

    Route::post('/publications/{id}/like', [PublicationReactionController::class, 'like'])
        ->name('publications.like');
    Route::post('/publications/{id}/dislike', [PublicationReactionController::class, 'dislike'])
        ->name('publications.dislike');
});
// Admin
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/components/avatars', 'admin.components.avatars')->name('admin.components.avatars');
    Route::view('/components/buttons', 'admin.components.buttons')->name('admin.components.buttons');
    Route::view('/components/gridsystem', 'admin.components.gridsystem')->name('admin.components.gridsystem');
    Route::view('/components/panels', 'admin.components.panels')->name('admin.components.panels');
    Route::view('/components/notifications', 'admin.components.notifications')->name('admin.components.notifications');
    Route::view('/components/sweetalert', 'admin.components.sweetalert')->name('admin.components.sweetalert');
    Route::view('/components/font-awesome-icons', 'admin.components.font-awesome-icons')->name('admin.components.fontawesome');
    Route::view('/components/simple-line-icons', 'admin.components.simple-line-icons')->name('admin.components.simpleline');
    Route::view('/components/typography', 'admin.components.typography')->name('admin.components.typography');
Route::get('/publications', [PublicationController::class, 'adminIndex'])->name('admin.publications.index');
Route::delete('/publications/{id}/delete', [PublicationController::class, 'adminDestroy'])->name('admin.publications.destroy');
    Route::get('/publications/export/csv', [PublicationController::class, 'exportCsv'])->name('admin.publications.export');
    Route::post('/publications/{id}/ban', [PublicationController::class, 'adminBanUser'])->name('admin.publications.ban');

    // New routes for commentaires
    Route::get('/commentaires', [CommentaireController::class, 'adminIndex'])->name('admin.commentaires.index');
    Route::delete('/commentaires/{id}/delete', [CommentaireController::class, 'adminDestroy'])->name('admin.commentaires.destroy');
Route::get('/commentaires/export/csv', [CommentaireController::class, 'exportCsv'])->name('admin.commentaires.export');
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


Route::post('partners/{partner}/ai-report', [PartnerController::class, 'aiReport'])
    ->name('admin.partners.ai_report')
    ->middleware('auth'); // remove 'can:manage-partners' while debugging/if you don't use gates




    // Users Management
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('users/{user}/toggle-verification', [AdminUserController::class, 'toggleVerification'])->name('admin.users.toggle-verification');
    Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::get('users/export/csv', [AdminUserController::class, 'export'])->name('admin.users.export');

    // Products Management
    Route::get('products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('products/{product}', [AdminProductController::class, 'show'])->name('admin.products.show');
    Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::patch('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');
    Route::post('products/bulk-action', [AdminProductController::class, 'bulkAction'])->name('admin.products.bulk-action');
    Route::get('products/export/csv', [AdminProductController::class, 'export'])->name('admin.products.export');
    Route::post('products/{product}/duplicate', [AdminProductController::class, 'duplicate'])->name('admin.products.duplicate');

    // Categories Management
    Route::get('categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('categories/{category}', [AdminCategoryController::class, 'show'])->name('admin.categories.show');
    Route::get('categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::patch('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
    Route::post('categories/bulk-action', [AdminCategoryController::class, 'bulkAction'])->name('admin.categories.bulk-action');
    Route::get('categories/list', [AdminCategoryController::class, 'getCategories'])->name('admin.categories.list');

    //tuto
Route::get('/tutos', [TutoController::class, 'adminIndex'])->name('admin.tutos.index');
Route::get('/tutos/create', [TutoController::class, 'create'])->name('admin.tutos.create');
Route::get('/tutos/{tuto}', [TutoController::class, 'adminShow'])->name('admin.tutos.show');
Route::post('/tutos', [TutoController::class, 'store'])->name('admin.tutos.store');
 Route::get('/tutos/{tuto}/edit', [TutoController::class, 'edit'])->name('admin.tutos.edit');
    Route::put('/tutos/{tuto}', [TutoController::class, 'update'])->name('admin.tutos.update');
    Route::delete('/tutos/{tuto}', [TutoController::class, 'destroy'])->name('admin.tutos.destroy');
    Route::delete('/questions/{question}', [TutoController::class, 'questionDestroy'])->name('questions.destroy');
    Route::post('/users/{user}/ban', [TutoController::class, 'banUser'])->name('banUser');
  // Quiz Management
    Route::get('/quizzes', [QuizController::class, 'adminIndex'])->name('admin.quizzes.index');
    Route::get('/quizzes/participants', [QuizController::class, 'participants'])->name('admin.quizzes.participants');
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('admin.quizzes.create');
    Route::post('/quizzes', [QuizController::class, 'store'])->name('admin.quizzes.store');
    Route::get('/quizzes/{quiz}', [QuizController::class, 'adminShow'])->name('admin.quizzes.show');
    Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('admin.quizzes.edit');
    Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('admin.quizzes.update');
    Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('admin.quizzes.destroy');
});











// Removed catch-all to external template pages to avoid dependency on kaiadmin-lite

// Legacy redirects: map old /admin/pages/* URLs to new Blade routes
Route::middleware(['auth', 'admin'])->group(function () {
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
});

Route::get('/tutos', [TutoController::class, 'index'])->name('tutos.index');
Route::get('/tutos/{tuto}', [TutoController::class, 'show'])->name('tutos.show');
Route::post('/tutos/{tuto}/react', [TutoController::class, 'react'])->name('tutos.react')->middleware('auth');
Route::post('/tutos/{tuto}/question', [TutoController::class, 'askQuestion'])->name('tutos.question')->middleware('auth');
Route::get('/tutos/{tuto}/certificates/upload', [TutoController::class, 'uploadCertificate'])->name('certificates.upload')->middleware('auth');
Route::get('/tutos/{tuto}/certificates/upload', [TutoController::class, 'uploadCertificate'])->name('certificates.upload')->middleware('auth');
Route::post('/tutos/{tuto}/certificates/upload', [TutoController::class, 'generateCertificate'])->name('certificates.generate')->middleware('auth');
Route::post('/tutos/{tuto}/certificates/generate', [TutoController::class, 'generateCertificate'])
    ->name('certificates.generate');


    Route::get('/tutos/{tuto}/certificate', [TutoController::class, 'showCertificate'])->name('certificates.show')->middleware('auth');
    Route::post('/tutos/{tuto}/certificate/download', [TutoController::class, 'downloadCertificate'])->name('certificates.download')->middleware('auth');



// Frontend Quiz Routes
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show')->middleware('auth');
Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit')->middleware('auth');
Route::get('/test-pdfco', function () {
    try {
        $client = new Client(['verify' => false]);
        $response = $client->post('https://api.pdf.co/v1/pdf/convert/from/html', [
            'headers' => [
                'x-api-key' => env('PDFCO_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'html' => '<h1>Test PDF.co fonctionne !</h1>',
                'name' => 'test.pdf',
                'inline' => true,
            ],
        ]);

        $result = json_decode($response->getBody(), true);

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
  