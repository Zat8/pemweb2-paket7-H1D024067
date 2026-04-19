<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route per Role (mengganti route /dashboard bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'panitia' => redirect()->route('panitia.dashboard'),
            default => redirect()->route('peserta.dashboard'),
        };
    })->name('dashboard');

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

// ========================================
// ROUTES PAKET 7 - EVENT & REGISTRASI
// ========================================

// Public Routes (Katalog Event)
Route::get('/events', [App\Http\Controllers\EventController::class, 'index'])->name('events.public.index');
Route::get('/events/{event:slug}', [App\Http\Controllers\EventController::class, 'show'])->name('events.public.show');

// Protected Routes - Admin & Panitia Only
Route::middleware(['auth', 'role:admin,panitia'])->prefix('admin/events')->name('events.admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\EventController::class, 'adminIndex'])->name('index');
    Route::get('/create', [App\Http\Controllers\EventController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\EventController::class, 'store'])->name('store');
    Route::get('/{event}/edit', [App\Http\Controllers\EventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [App\Http\Controllers\EventController::class, 'update'])->name('update');
    Route::delete('/{event}', [App\Http\Controllers\EventController::class, 'destroy'])->name('destroy');
});

// Dashboard Routes per Role (Update dari Fase 4)
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        $totalEvents = \App\Models\Event::count();
        $totalParticipants = \App\Models\User::where('role', 'peserta')->count();
        return view('dashboard', compact('totalEvents', 'totalParticipants'));
    })->middleware('role:admin')->name('admin.dashboard');

    Route::get('/panitia/dashboard', function () {
        $myEvents = \App\Models\Event::where('created_by', auth()->id())->count();
        return view('dashboard', compact('myEvents'));
    })->middleware('role:panitia')->name('panitia.dashboard');

    Route::get('/peserta/dashboard', function () {
        $myRegistrations = auth()->user()->registrations()->with('event')->get();
        return view('dashboard', compact('myRegistrations'));
    })->middleware('role:peserta')->name('peserta.dashboard');
});

require __DIR__.'/auth.php';
