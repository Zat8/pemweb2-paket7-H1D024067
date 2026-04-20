<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    @php
        $quotaPercent = $event->quota > 0 ? min(100, ($event->registrations_count / $event->quota) * 100) : 0;
    @endphp

    <div class="py-12">
        <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
                    <p class="font-semibold">Berhasil</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 shadow-sm">
                    <p class="font-semibold">Perlu perhatian</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-slate-200">
                @if($event->poster)
                    <div class="h-80 w-full overflow-hidden bg-slate-900">
                        <img src="{{ Storage::url($event->poster) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                    </div>
                @else
                    <div class="flex h-80 items-center justify-center bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_28%),linear-gradient(135deg,#f8fafc_0%,#e2e8f0_45%,#cffafe_100%)] px-8 text-center text-slate-900">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-cyan-800">{{ $event->category->name }}</p>
                            <h1 class="mt-4 text-4xl font-black md:text-5xl">{{ $event->title }}</h1>
                        </div>
                    </div>
                @endif

                <div class="grid gap-0 lg:grid-cols-[1.6fr_0.9fr]">
                    <section class="p-8 lg:p-10">
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center rounded-full bg-cyan-100 px-4 py-2 text-sm font-semibold text-cyan-800">
                                {{ $event->category->name }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold {{ $isFull ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $isFull ? 'Kuota penuh' : 'Pendaftaran tersedia' }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold
                                @if($event->status === 'published') bg-sky-100 text-sky-800
                                @elseif($event->status === 'draft') bg-slate-100 text-slate-700
                                @elseif($event->status === 'closed') bg-amber-100 text-amber-800
                                @else bg-violet-100 text-violet-800
                                @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>

                        <h1 class="mt-6 text-4xl font-black tracking-tight text-slate-900">{{ $event->title }}</h1>

                        @if($event->speaker)
                            <p class="mt-3 text-lg text-slate-600">
                                Pembicara: <span class="font-semibold text-slate-900">{{ $event->speaker }}</span>
                            </p>
                        @endif

                        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tanggal</p>
                                <p class="mt-3 text-lg font-bold text-slate-900">{{ \Carbon\Carbon::parse($event->event_date)->isoFormat('dddd, D MMMM YYYY') }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Waktu</p>
                                <p class="mt-3 text-lg font-bold text-slate-900">{{ $event->start_time }} - {{ $event->end_time }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Lokasi</p>
                                <p class="mt-3 text-lg font-bold text-slate-900">{{ $event->location }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Kuota</p>
                                <p class="mt-3 text-lg font-bold text-slate-900">{{ $event->registrations_count }}/{{ $event->quota }} peserta</p>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-cyan-500" style="width: {{ $quotaPercent }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <h3 class="text-2xl font-bold text-slate-900">Deskripsi Event</h3>
                            <div class="mt-4 rounded-3xl bg-slate-50 p-6 text-slate-700 ring-1 ring-slate-200">
                                <p class="whitespace-pre-line leading-8">{{ $event->description }}</p>
                            </div>
                        </div>
                    </section>

                    <aside class="border-t border-slate-200 bg-slate-50 p-8 lg:border-l lg:border-t-0 lg:p-10">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-700">Pendaftaran</p>

                        @auth
                            @if(auth()->user()->role === 'peserta')
                                @if($isRegistered && $registration)
                                    <div class="mt-4 rounded-[2rem] bg-white p-7 shadow-sm ring-1 ring-emerald-200">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 h-3 w-3 rounded-full bg-emerald-500"></div>
                                            <div>
                                                <h3 class="text-xl font-bold text-emerald-800">Anda sudah terdaftar</h3>
                                                <p class="mt-1 text-sm text-slate-600">Tiket digital Anda aktif dan siap digunakan saat check-in.</p>
                                            </div>
                                        </div>

                                        <div class="mt-6 rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-200">
                                            <div class="flex justify-center">
                                                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                                                    {!! $registrationQrSvg !!}
                                                </div>
                                            </div>
                                            <p class="mt-4 text-center text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Token tiket</p>
                                            <p class="mt-3 break-all rounded-2xl bg-white px-5 py-4 text-center font-mono text-sm font-bold text-slate-800 ring-1 ring-slate-200">
                                                {{ $registration->ticket_token }}
                                            </p>
                                        </div>

                                        <div class="mt-4 space-y-3 text-sm">
                                            @if($registration->attendance)
                                                <div class="rounded-2xl bg-emerald-100 px-4 py-3 font-semibold text-emerald-800">
                                                    Sudah check-in pada {{ \Carbon\Carbon::parse($registration->attendance->checked_in_at)->format('d M Y H:i') }}
                                                </div>
                                            @else
                                                <div class="rounded-2xl bg-amber-100 px-4 py-3 font-semibold text-amber-800">
                                                    Belum check-in
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-6 flex flex-col gap-3">
                                            <a href="{{ $registrationTicketUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-cyan-200 px-5 py-3 font-semibold text-slate-900 transition hover:bg-cyan-300">
                                                Lihat tiket lengkap
                                            </a>
                                            @if($registration->certificate)
                                                <a href="{{ route('certificates.download', $registration->certificate) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-200 px-5 py-3 font-semibold text-slate-900 transition hover:bg-emerald-300">
                                                    Unduh sertifikat
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($isFull)
                                    <div class="mt-4 rounded-[2rem] border border-rose-200 bg-white p-7 shadow-sm">
                                        <h3 class="text-xl font-bold text-rose-800">Kuota sudah penuh</h3>
                                        <p class="mt-2 text-sm text-slate-600">Event ini sudah mencapai batas maksimal peserta.</p>
                                        <button disabled class="mt-5 w-full rounded-2xl bg-slate-300 px-5 py-3 font-semibold text-slate-900">
                                            Pendaftaran ditutup
                                        </button>
                                    </div>
                                @else
                                    <div class="mt-4 rounded-[2rem] border border-cyan-200 bg-white p-7 shadow-sm">
                                        <h3 class="text-xl font-bold text-slate-900">Siap bergabung?</h3>
                                        <p class="mt-2 text-sm text-slate-600">
                                            Daftar sekarang untuk mengamankan kursi dan mendapatkan tiket digital otomatis.
                                        </p>

                                        <form method="POST" action="{{ route('registrations.store', $event->id) }}" class="mt-6 space-y-4">
                                            @csrf
                                            <button type="submit" class="w-full rounded-2xl bg-cyan-200 px-5 py-4 text-base font-bold text-slate-900 transition hover:bg-cyan-300">
                                                Daftar Sekarang
                                            </button>
                                            <p class="text-sm text-slate-500">
                                                Setelah mendaftar, tiket dapat langsung dibuka dari dashboard peserta.
                                            </p>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <div class="mt-4 rounded-[2rem] border border-amber-200 bg-white p-7 shadow-sm">
                                    <h3 class="text-xl font-bold text-amber-800">Login sebagai peserta</h3>
                                    <p class="mt-2 text-sm text-slate-600">Akun dengan role peserta diperlukan untuk mendaftar event ini.</p>
                                    <p class="mt-3 text-sm text-amber-700">Role Anda saat ini: <span class="font-semibold">{{ ucfirst(auth()->user()->role) }}</span></p>
                                </div>
                            @endif
                        @else
                            <div class="mt-4 rounded-[2rem] border border-sky-200 bg-white p-7 shadow-sm">
                                <h3 class="text-xl font-bold text-slate-900">Login untuk mendaftar</h3>
                                <p class="mt-2 text-sm text-slate-600">Masuk dengan akun peserta agar bisa mendapatkan tiket digital event ini.</p>
                                <a href="{{ route('login') . '?redirect=' . url()->current() }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-sky-200 px-5 py-3 font-semibold text-slate-900 transition hover:bg-sky-300">
                                    Login sekarang
                                </a>
                                <p class="mt-4 text-sm text-slate-500">
                                    Belum punya akun?
                                    <a href="{{ route('register') }}" class="font-semibold text-sky-700 underline">Daftar di sini</a>
                                </p>
                            </div>
                        @endauth

                        <div class="mt-6 rounded-[2rem] bg-slate-100 p-7 text-sm text-slate-700 ring-1 ring-slate-200">
                            <p class="font-semibold uppercase tracking-[0.25em] text-cyan-800">Panduan</p>
                            <ol class="mt-4 list-decimal space-y-3 pl-6 leading-7">
                                <li>Selesaikan pendaftaran dengan akun peserta.</li>
                                <li>Buka tiket dari dashboard atau halaman event ini.</li>
                                <li>Tunjukkan QR code atau token saat check-in di lokasi.</li>
                            </ol>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
