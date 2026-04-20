<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Menampilkan halaman tiket QR Code.
     */
    public function show(string $token)
    {
        $normalizedToken = strtoupper(trim($token));

        $reg = Registration::with(['event', 'user', 'attendance', 'certificate'])
            ->whereRaw('UPPER(ticket_token) = ?', [$normalizedToken])
            ->first();

        if (! $reg) {
            abort(404, 'Tiket tidak ditemukan atau token salah.');
        }

        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array(Auth::user()->role, ['admin', 'panitia'], true) && $reg->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk melihat tiket ini.');
        }

        $ticketUrl = route('tickets.show', $reg->ticket_token);
        $qrSvg = QrCode::format('svg')
            ->size(280)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($ticketUrl);

        return view('peserta.ticket', compact('reg', 'ticketUrl', 'qrSvg'));
    }
}
