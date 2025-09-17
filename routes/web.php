<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BlogController;

// Routes principales
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes d'authentification
Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/signup', [AuthController::class, 'store'])->name('signup.store');

// Routes API JWT
Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Routes des pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/testimonial', [PageController::class, 'testimonial'])->name('testimonial');
Route::get('/feature', [PageController::class, 'feature'])->name('feature');

// Routes des produits
Route::get('/products', [ProductController::class, 'index'])->name('products');

// Routes de la boutique
Route::get('/store', [StoreController::class, 'index'])->name('store');

// Routes du blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
