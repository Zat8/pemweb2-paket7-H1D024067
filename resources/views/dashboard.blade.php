<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} - {{ ucfirst(auth()->user()->role) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(auth()->user()->role === 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Event</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ \App\Models\Event::count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Peserta</h3>
                        <p class="text-3xl font-bold text-green-600">{{ \App\Models\User::where('role', 'peserta')->count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Panitia</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ \App\Models\User::where('role', 'panitia')->count() }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('events.admin.index') }}" class="inline-block bg-yellow-300 text-black px-6 py-3 rounded-md hover:bg-yellow-400">
                        Kelola Event
                    </a>
                    <a href="{{ route('event-categories.index') }}" class="inline-block bg-white text-black px-6 py-3 rounded-md border border-gray-300 hover:bg-gray-50">
                        Kelola Kategori
                    </a>
                </div>
            @elseif(auth()->user()->role === 'panitia')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Event Saya</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ auth()->user()->createdEvents()->count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Pendaftar</h3>
                        <p class="text-3xl font-bold text-green-600">
                            {{ auth()->user()->createdEvents()->withCount('registrations')->get()->sum('registrations_count') }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('events.admin.index') }}" class="inline-block bg-yellow-300 text-black px-6 py-3 rounded-md hover:bg-yellow-400">
                    Kelola Event Saya
                </a>
            @else
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Event yang Saya Daftari</h3>

                    @forelse(auth()->user()->registrations()->with(['event', 'attendance', 'certificate'])->get() as $reg)
                        <div class="flex flex-col gap-4 py-4 border-b last:border-0 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-start gap-4">
                                @if($reg->qr_path)
                                    <img src="{{ Storage::url($reg->qr_path) }}" alt="QR tiket {{ $reg->event->title }}" class="h-24 w-24 rounded border bg-white p-2">
                                @endif
                                <div>
                                    <p class="font-medium">{{ $reg->event->title }}</p>
                                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($reg->event->event_date)->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-500">
                                        Token tiket: <span class="font-semibold text-gray-700">{{ $reg->ticket_token }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Status:
                                        @if($reg->attendance)
                                            <span class="font-semibold text-green-600">Sudah hadir</span>
                                        @else
                                            <span class="font-semibold text-yellow-600">Belum check-in</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3 md:justify-end">
                                <a href="{{ route('registrations.ticket', $reg) }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    Unduh Tiket
                                </a>
                                @if($reg->certificate)
                                    <a href="{{ route('certificates.download', $reg->certificate) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                        Unduh Sertifikat
                                    </a>
                                @endif
                                <a href="{{ route('events.public.show', $reg->event->slug) }}" class="inline-flex items-center rounded-md border border-yellow-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-yellow-50">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Belum ada event yang didaftarkan.</p>
                    @endforelse
                </div>

                <a href="{{ route('events.public.index') }}" class="inline-block bg-yellow-300 text-black px-6 py-3 rounded-md hover:bg-yellow-400">
                    Jelajahi Event Lainnya
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
