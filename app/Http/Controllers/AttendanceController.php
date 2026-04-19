<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index()
    {
        $events = Auth::user()->role === 'admin'
            ? Event::where('status', 'published')->orderBy('event_date', 'desc')->get()
            : Auth::user()->createdEvents()->orderBy('event_date', 'desc')->get();

        return view('panitia.attendance', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ticket_token' => ['required', 'string'],
        ]);

        $token = strtoupper(trim($request->ticket_token));

        $registration = Registration::where('ticket_token', $token)
            ->with(['event', 'user', 'attendance'])
            ->first();

        if (! $registration) {
            return back()->with('error', 'Token tiket tidak ditemukan.');
        }

        $event = $registration->event;

        if ($registration->attendance) {
            return back()->with(
                'success',
                "{$registration->user->name} sudah pernah absen pada {$registration->attendance->checked_in_at->format('H:i')}."
            );
        }

        if (now()->toDateString() !== $event->event_date) {
            return back()->with(
                'error',
                'Absensi hanya berlaku pada tanggal event: '.Carbon::parse($event->event_date)->format('d M Y')
            );
        }

        Attendance::create([
            'registration_id' => $registration->id,
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
        ]);

        $certificateNumber = 'CERT-'.strtoupper(Str::random(6)).'-'.date('Y');
        $certificatePath = 'certificates/'.$certificateNumber.'.pdf';

        Certificate::create([
            'registration_id' => $registration->id,
            'certificate_number' => $certificateNumber,
            'file_path' => $certificatePath,
            'issued_at' => now(),
        ]);

        Pdf::loadView('certificates.pdf', [
            'name' => $registration->user->name,
            'event' => $event->title,
            'date' => Carbon::parse($event->event_date)->isoFormat('D MMMM YYYY'),
            'cert_number' => $certificateNumber,
            'location' => $event->location,
        ])->save(storage_path('app/public/'.$certificatePath));

        return back()->with(
            'success',
            "Absen berhasil. Sertifikat untuk {$registration->user->name} telah dibuat otomatis."
        );
    }

    public function export(Event $event): StreamedResponse
    {
        if (Auth::user()->role !== 'admin' && $event->created_by !== Auth::id()) {
            abort(403);
        }

        $filename = 'kehadiran_'.Str::slug($event->title).'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($event) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Nama Peserta', 'Email', 'Institusi', 'Token Tiket', 'Waktu Check-In', 'Diverifikasi Oleh']);

            $event->attendances()
                ->with(['registration.user', 'checkedBy'])
                ->orderBy('checked_in_at')
                ->get()
                ->each(function (Attendance $attendance) use ($handle) {
                    fputcsv($handle, [
                        $attendance->registration->user->name,
                        $attendance->registration->user->email,
                        $attendance->registration->user->institution ?? '-',
                        $attendance->registration->ticket_token,
                        optional($attendance->checked_in_at)->format('d M Y H:i:s'),
                        $attendance->checkedBy->name ?? 'System',
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
