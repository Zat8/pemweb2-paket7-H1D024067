<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Verifikasi Sertifikat') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow text-center">
                @if($cert)
                    <div class="text-green-500 text-6xl mb-4">✅</div>
                    <h3 class="text-2xl font-bold text-gray-800">Sertifikat VALID</h3>
                    <p class="text-gray-600 mt-2">Diterbitkan atas nama:</p>
                    <p class="text-xl font-semibold text-indigo-700">{{ $cert->registration->user->name }}</p>
                    <p class="mt-4 text-gray-500">Untuk event: <span class="font-medium">{{ $cert->registration->event->title }}</span></p>
                    <p class="mt-1 text-sm text-gray-400">Tanggal Terbit: {{ \Carbon\Carbon::parse($cert->issued_at)->format('d M Y H:i') }}</p>
                @else
                    <div class="text-red-500 text-6xl mb-4">❌</div>
                    <h3 class="text-2xl font-bold text-gray-800">Sertifikat TIDAK DITEMUKAN</h3>
                    <p class="text-gray-600 mt-2">Nomor sertifikat yang Anda masukkan tidak valid atau belum terdaftar di sistem.</p>
                @endif
                <form method="GET" action="{{ route('certificates.verify', ['certNumber' => request('certNumber') ?? '']) }}" class="mt-8 flex gap-2">
                    <input type="text" name="certNumber" placeholder="Masukkan Nomor Sertifikat (CERT-XXXXX-YYYY)" 
                           class="flex-1 border-gray-300 rounded-md uppercase" value="{{ request('certNumber') }}">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Cek</button>
                </form>
                <p class="text-xs text-gray-400 mt-4">Halaman ini dapat diakses publik tanpa login.</p>
            </div>
        </div>
    </div>
</x-app-layout>