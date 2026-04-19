<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route per Role (mengganti route /dashboard bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard'); // Sementara pakai view default Breeze
    })->middleware('role:admin')->name('admin.dashboard');

    Route::get('/panitia/dashboard', function () {
        return view('dashboard');
    })->middleware('role:panitia')->name('panitia.dashboard');

    Route::get('/peserta/dashboard', function () {
        return view('dashboard');
    })->middleware('role:peserta')->name('peserta.dashboard');

    // Route Profile bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';