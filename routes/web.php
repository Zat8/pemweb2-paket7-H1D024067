<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'panitia' => redirect()->route('panitia.dashboard'),
            default => redirect()->route('peserta.dashboard'),
        };
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->middleware('role:admin')->name('admin.dashboard');

    Route::get('/panitia/dashboard', function () {
        return view('dashboard');
    })->middleware('role:panitia')->name('panitia.dashboard');

    Route::get('/peserta/dashboard', function () {
        return view('dashboard');
    })->middleware('role:peserta')->name('peserta.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/events', [EventController::class, 'index'])->name('events.public.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.public.show');

Route::middleware(['auth', 'role:admin,panitia'])->prefix('admin/events')->name('events.admin.')->group(function () {
    Route::get('/', [EventController::class, 'adminIndex'])->name('index');
    Route::get('/create', [EventController::class, 'create'])->name('create');
    Route::post('/', [EventController::class, 'store'])->name('store');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin/event-categories')->name('event-categories.')->group(function () {
    Route::get('/', [EventCategoryController::class, 'index'])->name('index');
    Route::post('/', [EventCategoryController::class, 'store'])->name('store');
    Route::delete('/{eventCategory}', [EventCategoryController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'role:peserta'])->group(function () {
    Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
});

Route::middleware(['auth', 'role:admin,panitia'])->prefix('panitia/attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::post('/', [AttendanceController::class, 'store'])->name('store');
    Route::get('/export/{event}', [AttendanceController::class, 'export'])->name('export');
});

Route::get('/certificates/verify/{certNumber?}', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::middleware('auth')->get('/certificates/download/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');

require __DIR__.'/auth.php';
