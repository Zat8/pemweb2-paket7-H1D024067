<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Event') }}
            </h2>
            {{-- Tampilkan hanya untuk Admin & Panitia --}}
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'panitia']))
                <a href="{{ route('events.admin.create') }}" 
                class="bg-yellow-300 text-black px-4 py-2 rounded-md hover:bg-yellow-400 text-sm font-medium">
                    + Tambah Event
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Flash Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search Form -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari event..." 
                           class="flex-1 border-gray-300 rounded-md shadow-sm">
                    <select name="status" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status')==='draft'?'selected':'' }}>Draft</option>
                        <option value="published" {{ request('status')==='published'?'selected':'' }}>Published</option>
                        <option value="closed" {{ request('status')==='closed'?'selected':'' }}>Closed</option>
                        <option value="finished" {{ request('status')==='finished'?'selected':'' }}>Finished</option>
                    </select>
                    <button type="submit" class="bg-yellow-300 text-black px-4 py-2 rounded-md hover:bg-yellow-400">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kuota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($events as $event)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $event->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $event->category->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $event->registrations_count }}/{{ $event->quota }}
                                    @if($event->isFull())
                                        <span class="ml-2 rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">FULL</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'published' => 'bg-green-100 text-green-800',
                                            'closed' => 'bg-yellow-100 text-yellow-800',
                                            'finished' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$event->status] }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('events.admin.edit', $event) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('events.admin.destroy', $event) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Hapus event ini?')"
                                                class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada event. <a href="{{ route('events.admin.create') }}" class="text-indigo-600">Buat event pertama</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
