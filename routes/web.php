<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HallController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::prefix('admin')->middleware('auth')->group(function () {

    //MOVIES
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/create-movies', [AdminController::class, 'createMoviesForm']);
    Route::post('/movies', [AdminController::class, 'store']);
    Route::put('/movies/{id}', [AdminController::class, 'update']);
    Route::delete('/movies/{id}', [AdminController::class, 'destroy']);

    //HALL
    Route::get('/hall-builder', [HallController::class, 'layoutBuilder'])->name('admin.index');
    Route::post('/hall-store', [HallController::class, 'store'])->name('halls.store');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
