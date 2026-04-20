<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/events');

Route::get('/events', [EventController::class, 'index'])->name('events.public.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.public.show');
Route::get('/certificates/verify/{certNumber?}', [CertificateController::class, 'verify'])->name('certificates.verify');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'panitia' => redirect()->route('panitia.dashboard'),
            default => redirect()->route('peserta.dashboard'),
        };
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        $totalEvents = \App\Models\Event::count();
        $totalParticipants = \App\Models\User::where('role', 'peserta')->count();
        $totalCategories = \App\Models\EventCategory::count();

        return view('dashboard', compact('totalEvents', 'totalParticipants', 'totalCategories'));
    })->middleware('role:admin')->name('admin.dashboard');

    Route::get('/panitia/dashboard', function () {
        $myEvents = \App\Models\Event::where('created_by', auth()->id())->count();
        $totalRegistrations = \App\Models\Registration::whereHas('event', function ($q) {
            $q->where('created_by', auth()->id());
        })->count();

        return view('dashboard', compact('myEvents', 'totalRegistrations'));
    })->middleware('role:panitia')->name('panitia.dashboard');

    Route::get('/peserta/dashboard', function () {
        $registrations = auth()->user()->registrations()->with(['event', 'attendance', 'certificate'])->get();

        return view('dashboard', compact('registrations'));
    })->middleware('role:peserta')->name('peserta.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/tickets/{token}', [TicketController::class, 'show'])->name('tickets.show');

    Route::middleware('role:admin,panitia')->prefix('admin/events')->name('events.admin.')->group(function () {
        Route::get('/', [EventController::class, 'adminIndex'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:admin')->prefix('admin/event-categories')->name('event-categories.')->group(function () {
        Route::get('/', [EventCategoryController::class, 'index'])->name('index');
        Route::get('/create', [EventCategoryController::class, 'create'])->name('create');
        Route::post('/', [EventCategoryController::class, 'store'])->name('store');
        Route::get('/{eventCategory}/edit', [EventCategoryController::class, 'edit'])->name('edit');
        Route::put('/{eventCategory}', [EventCategoryController::class, 'update'])->name('update');
        Route::delete('/{eventCategory}', [EventCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:admin,panitia')->prefix('panitia/attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
        Route::get('/export/{event}', [AttendanceController::class, 'export'])->name('export');
    });

    Route::get('/certificates/download/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');
});

require __DIR__.'/auth.php';
