<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

//admin TODO ADMIN MIDDLEWARE
Route::get('/admin', [AdminController::class, 'createMoviesForm'])->name('admin.create-movies');
Route::post('/create-movies', [AdminController::class, 'updateMovies'])->name('update-movies');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
