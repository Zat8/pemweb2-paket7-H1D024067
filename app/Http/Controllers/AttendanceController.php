<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\Registration;
use App\Support\QrTokenSvg;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    public function index()
    {
        $events = Auth::user()->role === 'admin'
            ? Event::whereIn('status', ['published', 'closed', 'finished'])->withCount(['registrations', 'attendances'])->orderBy('event_date', 'desc')->get()
            : Auth::user()->createdEvents()->withCount(['registrations', 'attendances'])->orderBy('event_date', 'desc')->get();

        return view('panitia.attendance', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'ticket_token' => ['required', 'string'],
        ]);

        $token = strtoupper(trim($request->ticket_token));

        $user = Auth::user();
        $result = DB::transaction(function () use ($request, $token, $user) {
            $event = Event::query()
                ->whereKey($request->integer('event_id'))
                ->lockForUpdate()
                ->firstOrFail();

            if ($user->role !== 'admin' && $event->created_by !== $user->id) {
                abort(403);
            }

            $registration = Registration::query()
                ->where('event_id', $event->id)
                ->where('ticket_token', $token)
                ->with(['event', 'user', 'attendance', 'certificate'])
                ->lockForUpdate()
                ->first();

            if (! $registration) {
                return [
                    'type' => 'error',
                    'message' => 'Token tiket tidak valid untuk event yang dipilih.',
                ];
            }

            if ($registration->attendance) {
                return [
                    'type' => 'success',
                    'message' => "{$registration->user->name} sudah pernah absen pada {$registration->attendance->checked_in_at->format('H:i')}.",
                ];
            }

            if (now()->toDateString() !== $event->event_date) {
                return [
                    'type' => 'error',
                    'message' => 'Absensi hanya berlaku pada tanggal event: '.Carbon::parse($event->event_date)->format('d M Y'),
                ];
            }

            Attendance::create([
                'registration_id' => $registration->id,
                'checked_in_at' => now(),
                'checked_in_by' => $user->id,
            ]);

            $certificate = $registration->certificate;

            if (! $certificate) {
                $certificateNumber = $this->generateUniqueCertificateNumber();
                $certificatePath = 'certificates/'.$certificateNumber.'.pdf';
                $verifyUrl = route('certificates.verify', ['certNumber' => $certificateNumber]);
                $verifyQrSvg = QrTokenSvg::svg($verifyUrl, 29, 180);

                $certificate = Certificate::create([
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
                    'verify_url' => $verifyUrl,
                    'verify_qr_svg' => $verifyQrSvg,
                ])->save(storage_path('app/public/'.$certificatePath));
            }

            return [
                'type' => 'success',
                'message' => "Absen berhasil. Sertifikat untuk {$registration->user->name} telah dibuat otomatis.",
            ];
        });

        return back()->with($result['type'], $result['message']);
    }

    public function export(Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->created_by !== Auth::id()) {
            abort(403);
        }

        $filename = 'kehadiran_'.Str::slug($event->title).'_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new AttendanceExport($event), $filename);
    }

    protected function generateUniqueCertificateNumber(): string
    {
        do {
            $certificateNumber = 'CERT-'.strtoupper(Str::random(6)).'-'.date('Y');
        } while (Certificate::where('certificate_number', $certificateNumber)->exists());

        return $certificateNumber;
    }
}
