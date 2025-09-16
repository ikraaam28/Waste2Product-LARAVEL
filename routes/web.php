<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeaHouseController;

// Routes principales
Route::get('/', [TeaHouseController::class, 'index'])->name('home');
Route::get('/about', [TeaHouseController::class, 'about'])->name('about');
Route::get('/products', [TeaHouseController::class, 'products'])->name('products');
Route::get('/store', [TeaHouseController::class, 'store'])->name('store');
Route::get('/contact', [TeaHouseController::class, 'contact'])->name('contact');
Route::get('/blog', [TeaHouseController::class, 'blog'])->name('blog');
Route::get('/testimonial', [TeaHouseController::class, 'testimonial'])->name('testimonial');
Route::get('/feature', [TeaHouseController::class, 'feature'])->name('feature');
Route::get('/signup', [TeaHouseController::class, 'signup'])->name('signup');
