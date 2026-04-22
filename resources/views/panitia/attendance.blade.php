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
                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-red-300 bg-red-100 px-4 py-3 text-red-700">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('attendance.store') }}" class="grid gap-3 md:grid-cols-[1fr_2fr_auto]">
                    @csrf
                    <select
                        name="event_id"
                        class="border-gray-300 rounded-md shadow-sm"
                        required
                    >
                        <option value="">Pilih event</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}" @selected((string) old('event_id') === (string) $ev->id)>
                                {{ $ev->title }} - {{ \Carbon\Carbon::parse($ev->event_date)->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                    <input
                        type="text"
                        name="ticket_token"
                        value="{{ old('ticket_token') }}"
                        placeholder="Masukkan token tiket atau scan QR"
                        class="border-gray-300 rounded-md shadow-sm text-lg uppercase tracking-wider"
                        required
                        autofocus
                    >
                    <button type="submit" class="bg-indigo-600 text-black px-6 py-2 rounded-md hover:bg-indigo-700 font-semibold">
                        Validasi Absen
                    </button>
                </form>

                <p class="text-sm text-gray-500 mt-2">
                    Tip: pilih event terlebih dahulu. Token bersifat case-insensitive dan hanya valid untuk event terkait pada tanggal pelaksanaan event.
                </p>

                <div class="mt-8">
                    <h3 class="font-bold text-gray-700 mb-3">Daftar Event Tersedia</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($events as $ev)
                            <div class="border p-4 rounded hover:bg-gray-50">
                                <p class="font-semibold">{{ $ev->title }}</p>
                                <p class="text-sm text-gray-500">
                                    Tanggal {{ \Carbon\Carbon::parse($ev->event_date)->format('d M Y') }}
                                    | Hadir: {{ $ev->attendances_count }}/{{ $ev->quota }}
                                </p>
                                <p class="text-xs mt-2 {{ $ev->registrations_count >= $ev->quota ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    Pendaftar: {{ $ev->registrations_count }}/{{ $ev->quota }}
                                    @if($ev->registrations_count >= $ev->quota)
                                        - FULL
                                    @endif
                                </p>
                                <a href="{{ route('attendance.export', $ev->id) }}" class="text-xs text-indigo-600 hover:underline mt-2 inline-block">
                                    Unduh Excel Kehadiran
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
