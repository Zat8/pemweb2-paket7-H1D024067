<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tiket Digital Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-slate-200">
                <div class="bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_32%),linear-gradient(135deg,#f8fafc_0%,#e2e8f0_48%,#cffafe_100%)] px-10 py-10 text-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-cyan-800">Tiket Digital</p>
                    <h3 class="mt-3 text-3xl font-black">{{ $reg->event->title }}</h3>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-700">
                        Simpan halaman ini atau unduh QR code untuk ditunjukkan saat absensi.
                    </p>
                </div>

                <div class="grid gap-0 lg:grid-cols-[1.1fr_0.9fr]">
                    <section class="p-8 lg:p-10">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tanggal</p>
                                <p class="mt-3 text-lg font-bold text-slate-900">{{ \Carbon\Carbon::parse($reg->event->event_date)->isoFormat('dddd, D MMMM YYYY') }}</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Lokasi</p>
                                <p class="mt-3 text-lg font-bold text-slate-900">{{ $reg->event->location }}</p>
                            </div>
                        </div>

                        <div class="mt-6 rounded-[2rem] bg-slate-100 p-7 ring-1 ring-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-cyan-800">Token tiket</p>
                            <p class="mt-4 break-all rounded-2xl bg-white px-5 py-5 font-mono text-sm font-bold text-slate-900 ring-1 ring-slate-200">
                                {{ $reg->ticket_token }}
                            </p>
                            <p class="mt-4 text-sm leading-7 text-slate-700">
                                Jika QR sulit dipindai, panitia tetap bisa memvalidasi token ini.
                            </p>
                        </div>

                        <div class="mt-6 rounded-[2rem] bg-slate-50 p-7 ring-1 ring-slate-200">
                            <h4 class="text-lg font-bold text-slate-900">Data Peserta</h4>
                            <dl class="mt-5 space-y-5">
                                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-slate-200">
                                    <dt class="text-sm text-slate-500">Nama</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $reg->user->name }}</dd>
                                </div>
                                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-slate-200">
                                    <dt class="text-sm text-slate-500">Email</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">{{ $reg->user->email }}</dd>
                                </div>
                                @if($reg->user->institution)
                                    <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-slate-200">
                                        <dt class="text-sm text-slate-500">Institusi</dt>
                                        <dd class="mt-1 font-semibold text-slate-900">{{ $reg->user->institution }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div class="mt-6">
                            @if($reg->attendance)
                                <div class="rounded-2xl bg-emerald-100 px-5 py-4 font-semibold text-emerald-800">
                                    Sudah hadir pada {{ \Carbon\Carbon::parse($reg->attendance->checked_in_at)->format('d M Y H:i') }}
                                </div>
                            @else
                                <div class="rounded-2xl bg-amber-100 px-5 py-4 font-semibold text-amber-800">
                                    Belum check-in
                                </div>
                            @endif
                        </div>
                    </section>

                    <aside class="border-t border-slate-200 bg-slate-50 p-8 lg:border-l lg:border-t-0 lg:p-10">
                        <div class="rounded-[2rem] bg-white p-7 text-center shadow-sm ring-1 ring-slate-200">
                            <div id="qr-download-source" class="mx-auto inline-flex rounded-[1.5rem] bg-white p-5 shadow-sm ring-1 ring-slate-200">
                                {!! $qrSvg !!}
                            </div>
                            <p class="mt-5 px-3 text-sm leading-7 text-slate-600">QR ini mengarah ke tiket Anda dan siap dipindai saat registrasi ulang.</p>
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            <a href="{{ route('peserta.dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-100">
                                Kembali ke dashboard
                            </a>
                            <button type="button" onclick="downloadQR()" class="inline-flex items-center justify-center rounded-2xl bg-cyan-200 px-5 py-3 font-semibold text-slate-900 transition hover:bg-cyan-300">
                                Unduh QR Code
                            </button>
                            @if($reg->certificate)
                                <a href="{{ route('certificates.download', $reg->certificate) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-200 px-5 py-3 font-semibold text-slate-900 transition hover:bg-emerald-300">
                                    Unduh Sertifikat
                                </a>
                            @endif
                        </div>

                        <div class="mt-6 rounded-[2rem] bg-sky-50 p-6 text-sm text-sky-900 ring-1 ring-sky-200">
                            <p class="font-semibold uppercase tracking-[0.25em] text-sky-700">Cara penggunaan</p>
                            <ol class="mt-4 list-decimal space-y-3 pl-6 leading-7">
                                <li>Tunjukkan QR code ini kepada panitia saat absensi.</li>
                                <li>Pastikan layar cukup terang agar mudah dipindai.</li>
                                <li>Gunakan token tiket bila QR tidak dapat discan.</li>
                            </ol>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadQR() {
            const source = document.querySelector('#qr-download-source svg');

            if (!source) {
                window.alert('QR code belum siap. Silakan coba lagi.');
                return;
            }

            const serializer = new XMLSerializer();
            const svgText = serializer.serializeToString(source);
            const blob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = 'QR-Tiket-{{ $reg->event->slug }}-{{ substr($reg->ticket_token, 0, 8) }}.svg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>
</x-app-layout>
