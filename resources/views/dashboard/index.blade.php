<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buleleng 360 — Dashboard Pemantauan</title>
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
</head>

<body class="h-screen w-screen overflow-hidden bg-[#07111f] text-slate-100 antialiased">

    <div class="grid h-screen grid-cols-[minmax(250px,300px)_1fr_minmax(250px,300px)]">

        {{-- ==================== KIRI: ORMAS & PARTAI ==================== --}}
        <aside class="flex flex-col overflow-hidden border-r border-cyan-400/20 bg-[#0b1829]">

            {{-- Ormas --}}
            <section class="flex flex-1 flex-col overflow-hidden border-b border-cyan-400/20">
                <div class="border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-cyan-300">Organisasi Masyarakat</h2>
                        <label class="flex shrink-0 items-center gap-2 text-xs text-slate-300">
                            <input type="checkbox" data-layer-toggle="ormas" class="h-4 w-4 rounded border-slate-500 bg-slate-800 text-cyan-400 focus:ring-cyan-400">
                            <span>Tampilkan</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Pilih kecamatan untuk menampilkan sekretariat.</p>
                </div>
                <div class="flex-1 space-y-0.5 overflow-y-auto px-2 py-2" data-radio-group="ormas">
                    @forelse ($kecamatanList as $kec)
                    <label class="flex cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-1.5 text-sm text-slate-300 transition hover:bg-cyan-400/10">
                        <input
                            type="radio"
                            name="ormas_kecamatan"
                            value="{{ $kec->id }}"
                            class="h-3.5 w-3.5 border-slate-500 bg-slate-800 text-cyan-400 focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 focus:ring-offset-[#0b1829]">
                        <span>{{ $kec->nama }}</span>
                    </label>
                    @empty
                    <p class="px-2.5 py-1.5 text-sm text-slate-500">Belum ada data kecamatan.</p>
                    @endforelse
                </div>
            </section>

            {{-- Partai --}}
            <section class="flex flex-1 flex-col overflow-hidden">
                <div class="border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-lime-300">Partai Politik</h2>
                        <label class="flex shrink-0 items-center gap-2 text-xs text-slate-300">
                            <input type="checkbox" data-layer-toggle="partai" class="h-4 w-4 rounded border-slate-500 bg-slate-800 text-lime-400 focus:ring-lime-400">
                            <span>Tampilkan</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Pilih kecamatan untuk menampilkan sekretariat.</p>
                </div>
                <div class="flex-1 space-y-0.5 overflow-y-auto px-2 py-2" data-radio-group="partai">
                    @forelse ($kecamatanList as $kec)
                    <label class="flex cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-1.5 text-sm text-slate-300 transition hover:bg-lime-400/10">
                        <input
                            type="radio"
                            name="partai_kecamatan"
                            value="{{ $kec->id }}"
                            class="h-3.5 w-3.5 border-slate-500 bg-slate-800 text-lime-400 focus:ring-2 focus:ring-lime-400 focus:ring-offset-1 focus:ring-offset-[#0b1829]">
                        <span>{{ $kec->nama }}</span>
                    </label>
                    @empty
                    <p class="px-2.5 py-1.5 text-sm text-slate-500">Belum ada data kecamatan.</p>
                    @endforelse
                </div>
            </section>

        </aside>

        {{-- ==================== TENGAH: PETA ==================== --}}
        <main class="relative">
            <div id="map" class="h-full w-full"></div>

            {{-- Judul kecil mengambang di pojok kiri-atas peta, identitas dashboard --}}
            <div class="pointer-events-none absolute left-4 top-4 z-[1000] rounded-md border border-cyan-300/30 bg-[#0b1829]/90 px-3 py-1.5 shadow-lg shadow-cyan-950/30 backdrop-blur">
                <p class="text-sm font-semibold text-cyan-200">Buleleng 360 <span class="text-lime-300">/ LIVE MAP</span></p>
            </div>
        </main>

        {{-- ==================== KANAN: AGAMA & KONFLIK ==================== --}}
        <aside class="flex flex-col overflow-hidden border-l border-cyan-400/20 bg-[#0b1829]">

            {{-- Agama --}}
            <section class="flex flex-1 flex-col overflow-hidden border-b border-cyan-400/20">
                <div class="border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-amber-300">Sebaran Agama</h2>
                        <label class="flex shrink-0 items-center gap-2 text-xs text-slate-300">
                            <input type="checkbox" data-layer-toggle="agama" class="h-4 w-4 rounded border-slate-500 bg-slate-800 text-amber-400 focus:ring-amber-400">
                            <span>Tampilkan</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Satu pin per kecamatan dengan ringkasan penduduk.</p>
                </div>
                <div class="flex-1 space-y-0.5 overflow-y-auto px-2 py-2" data-radio-group="agama">
                    @forelse ($kecamatanList as $kec)
                    <label class="flex cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-1.5 text-sm text-slate-300 transition hover:bg-amber-400/10">
                        <input type="radio" name="agama_kecamatan" value="{{ $kec->id }}" class="h-3.5 w-3.5 border-slate-500 bg-slate-800 text-amber-400 focus:ring-2 focus:ring-amber-400 focus:ring-offset-1 focus:ring-offset-[#0b1829]">
                        <span>{{ $kec->nama }}</span>
                    </label>
                    @empty
                    <p class="px-2.5 py-1.5 text-sm text-slate-500">Belum ada data kecamatan.</p>
                    @endforelse
                </div>
            </section>

            {{-- Konflik: sengaja dikosongkan dulu --}}
            <section class="flex flex-1 flex-col overflow-hidden">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Data Konflik</h2>
                </div>
                <div class="flex flex-1 items-center justify-center px-4">
                    <p class="text-sm text-slate-500">Segera hadir.</p>
                </div>
            </section>

        </aside>

    </div>
</body>

</html>