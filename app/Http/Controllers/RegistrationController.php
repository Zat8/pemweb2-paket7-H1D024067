<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    /**
     * Store a newly created registration in storage.
     */
    public function store(Event $event)
    {
        if (! Auth::check() || Auth::user()->role !== 'peserta') {
            return back()->with('error', 'Hanya akun peserta yang dapat mendaftar event.');
        }

        if ($event->status !== 'published') {
            return back()->with('error', 'Event ini sedang tidak dibuka untuk pendaftaran.');
        }

        if ($event->registrations()->count() >= $event->quota) {
            return back()->with('error', 'Kuota pendaftaran sudah penuh.');
        }

        $isRegistered = $event->registrations()->where('user_id', Auth::id())->exists();
        if ($isRegistered) {
            return back()->with('error', 'Anda sudah terdaftar di event ini.');
        }

        Registration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'ticket_token' => Str::upper(Str::random(40)),
            'registered_at' => now(),
        ]);

        return redirect()->route('peserta.dashboard')
            ->with('success', 'Pendaftaran berhasil. Tiket digital Anda sudah siap.');
    }
}
