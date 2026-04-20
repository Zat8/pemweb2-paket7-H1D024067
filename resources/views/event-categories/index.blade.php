<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Kategori Event</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-md border border-green-300 bg-green-100 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-md border border-red-300 bg-red-100 px-4 py-3 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-300 bg-red-100 px-4 py-3 text-red-700">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}
                        </h3>
                        @if($editingCategory)
                            <a href="{{ route('event-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                Batal Edit
                            </a>
                        @endif
                    </div>

                    <form
                        method="POST"
                        action="{{ $editingCategory ? route('event-categories.update', $editingCategory) : route('event-categories.store') }}"
                        class="space-y-4"
                    >
                        @csrf
                        @if($editingCategory)
                            @method('PUT')
                        @endif
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama kategori</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $editingCategory?->name) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Contoh: Seminar, Workshop"
                                required
                            >
                        </div>

                        <button type="submit" class="bg-yellow-300 text-black px-4 py-2 rounded-md hover:bg-yellow-400 font-medium">
                            {{ $editingCategory ? 'Perbarui Kategori' : 'Simpan Kategori' }}
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Event</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $category)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $category->events_count }} event</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('event-categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('event-categories.destroy', $category) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus kategori ini?')"
                                                class="text-red-600 hover:text-red-800"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada kategori event.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
