<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use App\Support\QrTokenSvg;

class RegistrationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
        ]);

        $user = Auth::user();

        if ($user->role !== 'peserta') {
            abort(403, 'Hanya peserta yang dapat mendaftar event.');
        }

        $result = DB::transaction(function () use ($validated, $user) {
            $event = Event::query()
                ->whereKey($validated['event_id'])
                ->lockForUpdate()
                ->withCount('registrations')
                ->firstOrFail();

            if ($event->status !== 'published') {
                return [
                    'type' => 'error',
                    'message' => 'Event ini belum tersedia untuk pendaftaran.',
                ];
            }

            if ($event->registrations()->where('user_id', $user->id)->exists()) {
                return [
                    'type' => 'success',
                    'message' => 'Anda sudah terdaftar pada event ini.',
                ];
            }

            if ($event->registrations_count >= $event->quota) {
                $event->update(['status' => 'closed']);

                return [
                    'type' => 'error',
                    'message' => 'Kuota event sudah penuh.',
                ];
            }

            $token = $this->generateUniqueTicketToken();
            $qrPath = QrTokenSvg::publicUrl(
                $this->ticketPayload($event->id, $token),
                'tickets/'.$token.'.svg'
            );

            Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'ticket_token' => $token,
                'qr_path' => $qrPath,
                'registered_at' => now(),
            ]);

            if ($event->registrations()->count() >= $event->quota) {
                $event->update(['status' => 'closed']);
            }

            return [
                'type' => 'success',
                'message' => 'Pendaftaran event berhasil. Tiket QR Anda sudah dibuat.',
            ];
        });

        return redirect()->route('peserta.dashboard')->with($result['type'], $result['message']);
    }

    public function downloadTicket(Registration $registration)
    {
        $user = Auth::user();

        if ($registration->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengunduh tiket peserta lain.');
        }

        if (! $registration->qr_path) {
            abort(404, 'File tiket belum tersedia.');
        }

        $absolutePath = storage_path('app/public/'.$registration->qr_path);

        if (! file_exists($absolutePath)) {
            abort(404, 'File tiket tidak ditemukan.');
        }

        return Response::download($absolutePath, 'Tiket_'.$registration->ticket_token.'.svg');
    }

    protected function generateUniqueTicketToken(): string
    {
        do {
            $token = strtoupper(Str::random(12));
        } while (Registration::where('ticket_token', $token)->exists());

        return $token;
    }

    protected function ticketPayload(int $eventId, string $token): string
    {
        return implode('|', [
            'event_id='.$eventId,
            'token='.$token,
        ]);
    }
}
