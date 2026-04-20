<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-red-300 bg-red-100 px-4 py-3 text-red-700">
                        <p class="font-semibold">Terjadi kesalahan:</p>
                        <ul class="mt-2 list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('events.admin.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Kategori -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Kategori *</label>
                        <select name="event_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('event_category_id')==$cat->id?'selected':'' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_category_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Judul -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Judul Event *</label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('title')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="4" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Speaker -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Pembicara</label>
                        <input type="text" name="speaker" value="{{ old('speaker') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('speaker')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tanggal & Waktu -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Event *</label>
                            <input type="date" name="event_date" value="{{ old('event_date') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('event_date')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jam Mulai *</label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('start_time')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jam Selesai *</label>
                            <input type="time" name="end_time" value="{{ old('end_time') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('end_time')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Lokasi & Kuota -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi *</label>
                            <input type="text" name="location" value="{{ old('location') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('location')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kuota Peserta *</label>
                            <input type="number" name="quota" value="{{ old('quota', 30) }}" min="1" max="1000"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('quota')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Upload Poster -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Poster Event (Opsional)</label>
                        <input type="file" name="poster" accept="image/*" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Maksimal 2MB. Format: JPG, PNG, GIF</p>
                        @error('poster')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="draft" {{ old('status')==='draft'?'selected':'' }}>Draft</option>
                            <option value="published" {{ old('status')==='published'?'selected':'' }}>Published</option>
                            <option value="closed" {{ old('status')==='closed'?'selected':'' }}>Closed</option>
                            <option value="finished" {{ old('status')==='finished'?'selected':'' }}>Finished</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('events.admin.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-yellow-300 text-black rounded-md hover:bg-yellow-400">
                            Simpan Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
