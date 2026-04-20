<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Halaman verifikasi publik sertifikat (Tantangan #76)
     * Dapat diakses tanpa login. Parameter opsional agar tidak error saat form kosong.
     */
    public function verify($certNumber = null)
    {
        $cert = null;
        $certNumber = $certNumber ?: request('certNumber');

        if ($certNumber) {
            $cert = Certificate::with(['registration.user', 'registration.event'])
                ->where('certificate_number', strtoupper(trim($certNumber)))
                ->first();
        }

        return view('certificates.verify', [
            'cert' => $cert,
            'searchedNumber' => strtoupper((string) $certNumber),
        ]);
    }

    /**
     * Download sertifikat PDF dengan validasi kepemilikan (Tantangan #77)
     * Hanya Admin atau Peserta pemilik sertifikat yang boleh mengunduh.
     */
    public function download(Certificate $certificate)
    {
        // 1. Wajib login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengunduh sertifikat.');
        }

        $user = Auth::user();

        // 2. Otorisasi: Admin boleh semua, Peserta hanya miliknya sendiri
        if ($user->role !== 'admin' && $certificate->registration->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk mengunduh sertifikat ini.');
        }

        // 3. Validasi file fisik di storage
        $filePath = storage_path('app/public/' . $certificate->file_path);
        if (!file_exists($filePath)) {
            abort(404, 'File PDF sertifikat belum digenerate atau telah dihapus dari server.');
        }

        // 4. Return response download
        return response()->download($filePath, 'Sertifikat_' . $certificate->certificate_number . '.pdf');
    }
}
