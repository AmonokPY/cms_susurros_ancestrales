<?php

use App\Http\Controllers\Admin\AuthorizedEmailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlayItemController;
use App\Http\Controllers\Admin\PuzzleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/puzzles', [PageController::class, 'puzzles'])->name('puzzles.index');
Route::get('/puzzles/{slug}', [PageController::class, 'puzzles'])->name('puzzles.show');
Route::get('/puzzle/{slug}', [PageController::class, 'puzzles'])->name('puzzles.alias');

Route::get('/acerca', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [PageController::class, 'contact'])->name('contact');
Route::post('/contacto', [PageController::class, 'sendContact'])
    ->middleware('throttle:contact')
    ->name('contact.send');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store'])
        ->middleware('throttle:register')
        ->name('register.store');
    Route::redirect('/register', '/registro');
    Route::post('/register', [RegisterController::class, 'store'])
        ->middleware('throttle:register');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'authorized'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('cms')->name('admin.')->group(function () {
        Route::middleware('can:manage-users')->group(function () {
            Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
            Route::patch('/usuarios/{user}/cms', [UserController::class, 'toggleCmsAccess'])->name('users.toggle-cms');
            Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::get('/correos-autorizados', [AuthorizedEmailController::class, 'index'])->name('authorized-emails.index');
            Route::post('/correos-autorizados', [AuthorizedEmailController::class, 'store'])->name('authorized-emails.store');
            Route::patch('/correos-autorizados/{authorizedEmail}/toggle', [AuthorizedEmailController::class, 'toggle'])->name('authorized-emails.toggle');
            Route::delete('/correos-autorizados/{authorizedEmail}', [AuthorizedEmailController::class, 'destroy'])->name('authorized-emails.destroy');
        });

        Route::middleware('can:manage-content')->group(function () {
            Route::get('/inicio', [SiteSettingController::class, 'edit'])->name('settings.edit');
            Route::put('/inicio', [SiteSettingController::class, 'update'])->name('settings.update');

            Route::post('play-items/reorder', [PlayItemController::class, 'reorder'])->name('play-items.reorder');
            Route::resource('play-items', PlayItemController::class)->except(['show']);

            Route::post('sponsors/reorder', [SponsorController::class, 'reorder'])->name('sponsors.reorder');
            Route::resource('sponsors', SponsorController::class)->except(['show']);
        });

        Route::middleware('can:manage-media')->group(function () {
            Route::post('puzzles/reorder', [PuzzleController::class, 'reorder'])->name('puzzles.reorder');
            Route::resource('puzzles', PuzzleController::class)->except(['show']);
        });
    });

    Route::resource('posts', PostController::class);
});
