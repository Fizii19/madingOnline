<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollVoteController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [PostController::class, 'beranda'])->name('home');

// Guest (not logged in) auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

// Logged-in users only
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin pages: only accessible to logged-in admins
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/management', [PostController::class, 'index'])->name('management');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
    Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
    Route::post('/polls/{poll}/toggle', [PollController::class, 'toggleActive'])->name('polls.toggle');
    Route::delete('/polls/{poll}', [PollController::class, 'destroy'])->name('polls.destroy');
});

// Public post detail (registered last so /posts/create wins over /posts/{post})
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Interactions require a logged-in user
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->middleware('auth')->name('posts.comments.store');
Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth')->name('posts.comments.destroy');
Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->middleware('auth')->name('posts.like');

// Poll voting requires a logged-in user
Route::post('/polls/{poll}/vote', [PollVoteController::class, 'store'])->middleware('auth')->name('polls.vote');
