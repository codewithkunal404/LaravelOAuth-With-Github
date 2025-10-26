<?php

use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 🔹 Basic Auth
Route::get('/login', [SocialAuthController::class, 'showLogin'])->name('login');
Route::get('/register', [SocialAuthController::class, 'showRegister'])->name('register');
Route::post('/login', [SocialAuthController::class, 'login'])->name('login.post');
Route::post('/register', [SocialAuthController::class, 'register'])->name('register.post');
Route::get('/logout', [SocialAuthController::class, 'logout'])->name('logout');

// 🔹 GitHub OAuth
Route::get('/auth/github', [SocialAuthController::class, 'redirectToGitHub'])->name('github.login');
Route::get('/auth/github/callback', [SocialAuthController::class, 'handleGitHubCallback']);


// 🔹 Google OAuth
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// 🔹 Protected Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');