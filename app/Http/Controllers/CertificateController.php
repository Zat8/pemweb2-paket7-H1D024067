<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function verify($certNumber)
    {
        $cert = Certificate::with(['registration.user', 'registration.event'])->where('certificate_number', strtoupper($certNumber))->first();
        return view('certificates.verify', compact('cert'));
    }

    public function download(Certificate $certificate)
    {
        // Validasi Kepemilikan (#77)
        if (Auth::user()->role !== 'admin' && $certificate->registration->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini.');
        }
        
        $filePath = storage_path('app/public/' . $certificate->file_path);
        if (!file_exists($filePath)) abort(404, 'File PDF belum digenerate.');
        return response()->download($filePath);
    }
}