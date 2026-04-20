<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if($event->poster)
                    <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                @endif

                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                            {{ $event->category->name }}
                        </span>
                        @if($isFull)
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                FULL
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <span class="text-gray-500 text-sm">Tanggal</span>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Waktu</span>
                            <p class="font-semibold">{{ $event->start_time }} - {{ $event->end_time }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Lokasi</span>
                            <p class="font-semibold text-sm">{{ $event->location }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Kuota</span>
                            <p class="font-semibold">{{ $event->registrations_count }}/{{ $event->quota }} peserta</p>
                        </div>
                    </div>

                    @if($event->speaker)
                        <div class="mb-4">
                            <span class="text-gray-500 text-sm">Pembicara</span>
                            <p class="font-semibold">{{ $event->speaker }}</p>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-2">Deskripsi</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                    </div>

                    <div class="border-t pt-4">
                        @auth
                            @if(auth()->user()->role === 'peserta')
                                @if($isRegistered)
                                    <div class="rounded-lg border border-green-200 bg-green-50 p-4 space-y-3">
                                        <a href="{{ route('peserta.dashboard') }}" class="block w-full text-center bg-green-600 text-white py-3 rounded-md font-semibold hover:bg-green-700">
                                            Anda Sudah Terdaftar
                                        </a>
                                        @if($registration?->qr_path)
                                            <div class="text-center">
                                                <img src="{{ Storage::url($registration->qr_path) }}" alt="QR tiket {{ $event->title }}" class="mx-auto h-48 w-48 rounded border border-green-200 bg-white p-3">
                                                <p class="mt-3 text-sm text-gray-600">Token tiket: <span class="font-semibold">{{ $registration->ticket_token }}</span></p>
                                                <div class="mt-3 flex flex-wrap justify-center gap-3">
                                                    <a href="{{ route('registrations.ticket', $registration) }}" class="inline-flex items-center rounded-md border border-green-600 px-4 py-2 text-sm font-semibold text-green-700 hover:bg-green-100">
                                                        Unduh Tiket
                                                    </a>
                                                    @if($registration->certificate)
                                                        <a href="{{ route('certificates.download', $registration->certificate) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                                            Unduh Sertifikat
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($isFull)
                                    <button disabled class="w-full bg-gray-400 text-white py-3 rounded-md font-semibold cursor-not-allowed">
                                        FULL
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('registrations.store') }}">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700">
                                            Daftar Sekarang
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 italic">
                                    Login sebagai peserta untuk mendaftar event ini.
                                </p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700">
                                Login untuk Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
