<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
        ]);

        $event = Event::withCount('registrations')->findOrFail($validated['event_id']);
        $user = Auth::user();

        if ($user->role !== 'peserta') {
            abort(403, 'Hanya peserta yang dapat mendaftar event.');
        }

        if ($event->status !== 'published') {
            return back()->with('error', 'Event ini belum tersedia untuk pendaftaran.');
        }

        if ($event->registrations()->where('user_id', $user->id)->exists()) {
            return redirect()->route('peserta.dashboard')->with('success', 'Anda sudah terdaftar pada event ini.');
        }

        if ($event->registrations_count >= $event->quota) {
            return back()->with('error', 'Kuota event sudah penuh.');
        }

        Registration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'ticket_token' => $this->generateUniqueTicketToken(),
            'registered_at' => now(),
        ]);

        return redirect()->route('peserta.dashboard')->with('success', 'Pendaftaran event berhasil.');
    }

    protected function generateUniqueTicketToken(): string
    {
        do {
            $token = strtoupper(Str::random(12));
        } while (Registration::where('ticket_token', $token)->exists());

        return $token;
    }
}
