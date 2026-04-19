<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Scan Absensi Peserta') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('attendance.store') }}" class="flex gap-3">
                    @csrf
                    <input type="text" name="ticket_token" placeholder="Masukkan Token Tiket (atau scan QR)" 
                           class="flex-1 border-gray-300 rounded-md shadow-sm text-lg uppercase tracking-wider" required autofocus>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-semibold">
                        ✅ Validasi Absen
                    </button>
                </form>
                <p class="text-sm text-gray-500 mt-2">💡 Tip: Token bersifat case-insensitive. Panitia bisa ketik manual atau gunakan scanner USB.</p>

                <div class="mt-8">
                    <h3 class="font-bold text-gray-700 mb-3">📋 Daftar Event Tersedia</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($events as $ev)
                            <div class="border p-4 rounded hover:bg-gray-50">
                                <p class="font-semibold">{{ $ev->title }}</p>
                                <p class="text-sm text-gray-500">📅 {{ \Carbon\Carbon::parse($ev->event_date)->format('d M Y') }} | 👥 Hadir: {{ $ev->attendances()->count() }}/{{ $ev->quota }}</p>
                                <a href="{{ route('attendance.export', $ev->id) }}" class="text-xs text-indigo-600 hover:underline mt-2 inline-block">📥 Export Excel Kehadiran</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>