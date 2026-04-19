<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} - {{ ucfirst(auth()->user()->role) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Flash Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- ADMIN DASHBOARD -->
            @if(auth()->user()->role === 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Event</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ \App\Models\Event::count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Peserta</h3>
                        <p class="text-3xl font-bold text-green-600">{{ \App\Models\User::where('role','peserta')->count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800">Total Panitia</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ \App\Models\User::where('role','panitia')->count() }}</p>
                    </div>
                </div>
                <a href="{{ route('events.admin.index') }}" 
                   class="inline-block bg-yellow-300 text-black px-6 py-3 rounded-md hover:bg-yellow-400">
                    → Kelola Event
                </a>

            <!-- PANITIA DASHBOARD -->
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
                <a href="{{ route('events.admin.index') }}" 
                   class="inline-block bg-yellow-300 text-black px-6 py-3 rounded-md hover:bg-yellow-400">
                    → Kelola Event Saya
                </a>

            <!-- PESERTA DASHBOARD -->
            @else
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🎟️ Event yang Saya Daftari</h3>
                    @forelse(auth()->user()->registrations()->with('event')->get() as $reg)
                        <div class="flex justify-between items-center py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium">{{ $reg->event->title }}</p>
                                <p class="text-sm text-gray-500">
                                    📅 {{ \Carbon\Carbon::parse($reg->event->event_date)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                @if($reg->attendance)
                                    <span class="text-green-600 text-sm font-semibold">✅ Sudah Hadir</span>
                                @elseif($reg->certificate)
                                    <a href="{{ route('certificates.download', $reg->certificate) }}" 
                                       class="text-indigo-600 text-sm font-semibold hover:underline">
                                        📄 Unduh Sertifikat
                                    </a>
                                @else
                                    <span class="text-yellow-600 text-sm">⏳ Menunggu Event</span>
                                @endif
                                <br>
                                <a href="{{ route('events.public.show', $reg->event->slug) }}" 
                                   class="text-xs text-gray-500 hover:text-gray-700">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Belum ada event yang didaftarkan.</p>
                    @endforelse
                </div>
                <a href="{{ route('events.public.index') }}" 
                   class="inline-block bg-yellow-300 text-black px-6 py-3 rounded-md hover:bg-yellow-400">
                    → Jelajahi Event Lainnya
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
