<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Verifikasi Sertifikat') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow text-center space-y-6">
                <div>
                    @if($cert)
                        <div class="mb-4 text-5xl font-black tracking-[0.3em] text-green-500">VALID</div>
                        <h3 class="text-2xl font-bold text-gray-800">Sertifikat Resmi Terverifikasi</h3>
                        <p class="mt-2 text-gray-600">Diterbitkan atas nama:</p>
                        <p class="text-xl font-semibold text-indigo-700">{{ $cert->registration->user->name }}</p>
                        <p class="mt-4 text-gray-500">Untuk event: <span class="font-medium">{{ $cert->registration->event->title }}</span></p>
                        <p class="mt-1 text-sm text-gray-400">Nomor sertifikat: {{ $cert->certificate_number }}</p>
                        <p class="mt-1 text-sm text-gray-400">Tanggal terbit: {{ \Carbon\Carbon::parse($cert->issued_at)->format('d M Y H:i') }}</p>

                        @auth
                            @if(auth()->user()->role === 'admin' || $cert->registration->user_id === auth()->id())
                                <a href="{{ route('certificates.download', $cert) }}" class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 font-semibold text-black hover:bg-indigo-700">
                                    Unduh Sertifikat
                                </a>
                            @endif
                        @endauth
                    @elseif($searchedNumber)
                        <div class="mb-4 text-5xl font-black tracking-[0.3em] text-red-500">INVALID</div>
                        <h3 class="text-2xl font-bold text-gray-800">Sertifikat Tidak Ditemukan</h3>
                        <p class="mt-2 text-gray-600">Nomor sertifikat yang Anda masukkan tidak valid atau belum terdaftar di sistem.</p>
                    @else
                        <div class="mb-4 text-5xl font-black tracking-[0.3em] text-indigo-500">CEK</div>
                        <h3 class="text-2xl font-bold text-gray-800">Cek Keaslian Sertifikat</h3>
                        <p class="mt-2 text-gray-600">Masukkan nomor sertifikat untuk memastikan keaslian dokumen secara publik tanpa login.</p>
                    @endif
                </div>

                <form method="GET" action="{{ route('certificates.verify') }}" class="flex gap-2">
                    <input
                        type="text"
                        name="certNumber"
                        placeholder="Masukkan Nomor Sertifikat (CERT-XXXXX-YYYY)"
                        class="flex-1 rounded-md border-gray-300 uppercase"
                        value="{{ request('certNumber', $searchedNumber) }}"
                    >
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-black">Cek</button>
                </form>

                <p class="text-xs text-gray-400">Halaman ini dapat diakses publik tanpa login.</p>
            </div>
        </div>
    </div>
</x-app-layout>
