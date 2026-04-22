<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <form method="GET" action="{{ route('events.public.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul event..."
                        class="border-gray-300 rounded-md shadow-sm"
                    >

                    <select name="category" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}" class="border-gray-300 rounded-md shadow-sm">

                    <button type="submit" class="bg-yellow-300 text-black px-4 py-2 rounded-md hover:bg-yellow-400">
                        Filter
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                        @if($event->poster)
                            <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No Poster</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-2 py-1 rounded">
                                {{ $event->category->name }}
                            </span>
                            <h3 class="font-bold text-lg mt-2">{{ $event->title }}</h3>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $event->description }}</p>

                            <div class="mt-4 space-y-2 text-sm text-gray-500">
                                <div>{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</div>
                                <div>{{ $event->start_time }} - {{ $event->end_time }}</div>
                                <div>{{ $event->location }}</div>
                                <div>Kuota: {{ $event->registrations_count }}/{{ $event->quota }}</div>
                            </div>

                            @php
                                $percent = min(100, ($event->registrations_count / max(1, $event->quota)) * 100);
                            @endphp

                            <div class="mt-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                                @if($event->isFull())
                                    <span class="text-xs text-red-600 font-semibold">FULL</span>
                                @endif
                            </div>

                            <a href="{{ route('events.public.show', $event->slug) }}" class="block mt-4 text-center bg-indigo-600 text-black py-2 rounded-md hover:bg-indigo-700">
                                Detail & Daftar
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        Tidak ada event yang tersedia.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
