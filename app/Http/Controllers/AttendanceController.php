<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Attendance;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Halaman Scan untuk Panitia
    public function index()
    {
        $events = Auth::user()->role === 'admin' 
            ? Event::where('status', 'published')->orderBy('event_date', 'desc')->get()
            : Auth::user()->createdEvents()->orderBy('event_date', 'desc')->get();
            
        return view('panitia.attendance', compact('events'));
    }

    // Proses Absensi + Generate Sertifikat Otomatis
    public function store(Request $request)
    {
        $request->validate(['ticket_token' => 'required|string']);
        $token = strtoupper(trim($request->ticket_token)); // Case insensitive
        
        $reg = Registration::where('ticket_token', $token)->with(['event', 'user'])->first();
        if (!$reg) return back()->with('error', '❌ Token tiket tidak ditemukan.');
        
        $event = $reg->event;
        
        // Validasi 1x Scan (#73)
        if ($reg->attendance) {
            return back()->with('success', "✅ {$reg->user->name} SUDAH PERNAH ABSEN pada {$reg->attendance->checked_in_at->format('H:i')}");
        }

        // Validasi Tanggal Event (#74)
        if (now()->toDateString() !== $event->event_date) {
            return back()->with('error', '⏳ Absensi hanya berlaku pada tanggal event: ' . Carbon::parse($event->event_date)->format('d M Y'));
        }

        // 1. Simpan Kehadiran
        $attendance = Attendance::create([
            'registration_id' => $reg->id,
            'checked_in_at' => now(),
            'checked_in_by' => Auth::id(),
        ]);

        // 2. Auto-Generate Sertifikat PDF (#75)
        $certNumber = 'CERT-' . strtoupper(Str::random(6)) . '-' . date('Y');
        $certPath = 'certificates/' . $certNumber . '.pdf';
        
        $cert = Certificate::create([
            'registration_id' => $reg->id,
            'certificate_number' => $certNumber,
            'file_path' => $certPath,
            'issued_at' => now(),
        ]);

        // Render & Simpan PDF
        $pdf = Pdf::loadView('certificates.pdf', [
            'name' => $reg->user->name,
            'event' => $event->title,
            'date' => Carbon::parse($event->event_date)->isoFormat('D MMMM YYYY'),
            'cert_number' => $certNumber,
            'location' => $event->location
        ])->save(storage_path('app/public/' . $certPath));

        return back()->with('success', "✅ ABSEN BERHASIL! Sertifikat untuk {$reg->user->name} telah digenerate otomatis.");
    }
}

public function export($eventId){
    $event = Event::findOrFail($eventId);
    // Validasi akses panitia/admin
    if (Auth::user()->role !== 'admin' && $event->created_by !== Auth::id()) abort(403);
        
    return (new \App\Exports\AttendanceExport($event))->download('Kehadiran_'.str_replace(' ','_',$event->title).'.xlsx');
}