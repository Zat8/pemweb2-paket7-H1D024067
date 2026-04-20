<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} - {{ ucfirst(auth()->user()->role) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if(auth()->user()->role === 'admin')
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Total Event</p>
                        <p class="mt-3 text-4xl font-black text-slate-900">{{ \App\Models\Event::count() }}</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Total Peserta</p>
                        <p class="mt-3 text-4xl font-black text-emerald-600">{{ \App\Models\User::where('role', 'peserta')->count() }}</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Total Panitia</p>
                        <p class="mt-3 text-4xl font-black text-sky-600">{{ \App\Models\User::where('role', 'panitia')->count() }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('events.admin.index') }}" class="inline-flex items-center rounded-xl bg-amber-300 px-5 py-3 font-semibold text-slate-900 transition hover:bg-amber-400">
                        Kelola Event
                    </a>
                    <a href="{{ route('event-categories.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50">
                        Kelola Kategori
                    </a>
                </div>
            @elseif(auth()->user()->role === 'panitia')
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Event Saya</p>
                        <p class="mt-3 text-4xl font-black text-slate-900">{{ auth()->user()->createdEvents()->count() }}</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Total Pendaftar</p>
                        <p class="mt-3 text-4xl font-black text-emerald-600">
                            {{ auth()->user()->createdEvents()->withCount('registrations')->get()->sum('registrations_count') }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('events.admin.index') }}" class="inline-flex items-center rounded-xl bg-amber-300 px-5 py-3 font-semibold text-slate-900 transition hover:bg-amber-400">
                    Kelola Event Saya
                </a>
            @elseif(auth()->user()->role === 'peserta')
                <section class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.16),_transparent_30%),linear-gradient(135deg,#f8fafc_0%,#e2e8f0_50%,#cffafe_100%)] px-8 py-8 text-slate-900">
                        <p class="text-sm uppercase tracking-[0.25em] text-cyan-800">Peserta</p>
                        <h3 class="mt-2 text-2xl font-black">Event yang Saya Ikuti</h3>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-700">
                            Semua tiket aktif, status kehadiran, dan sertifikat Anda ada di sini.
                        </p>
                    </div>

                    <div class="p-6">
                        @forelse(($registrations ?? collect()) as $reg)
                            <article class="mb-5 rounded-3xl border border-slate-200 bg-slate-50 p-5 last:mb-0">
                                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-cyan-700">
                                                {{ \Carbon\Carbon::parse($reg->event->event_date)->isoFormat('D MMMM YYYY') }}
                                            </p>
                                            <h4 class="mt-1 text-2xl font-bold text-slate-900">{{ $reg->event->title }}</h4>
                                            <p class="mt-1 text-sm text-slate-600">{{ $reg->event->location }}</p>
                                        </div>

                                        <div class="flex flex-wrap gap-3 text-sm">
                                            <span class="rounded-full bg-white px-4 py-2 font-mono text-slate-700 ring-1 ring-slate-200">
                                                {{ $reg->ticket_token }}
                                            </span>
                                            @if($reg->attendance)
                                                <span class="rounded-full bg-emerald-100 px-4 py-2 font-semibold text-emerald-700">
                                                    Sudah hadir pada {{ \Carbon\Carbon::parse($reg->attendance->checked_in_at)->format('d M Y H:i') }}
                                                </span>
                                            @else
                                                <span class="rounded-full bg-amber-100 px-4 py-2 font-semibold text-amber-700">
                                                    Belum check-in
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <a href="{{ route('tickets.show', $reg->ticket_token) }}" class="inline-flex items-center justify-center rounded-xl bg-cyan-200 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-300">
                                            Lihat Tiket
                                        </a>
                                        @if($reg->certificate)
                                            <a href="{{ route('certificates.download', $reg->certificate) }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-200 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-emerald-300">
                                                Unduh Sertifikat
                                            </a>
                                        @endif
                                        <a href="{{ route('events.public.show', $reg->event->slug) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                            Detail Event
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-600">
                                Belum ada event yang didaftarkan.
                            </div>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
