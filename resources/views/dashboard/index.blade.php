<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buleleng 360 — Dashboard Pemantauan</title>
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
</head>

<body class="h-screen w-screen overflow-hidden bg-[#07111f] text-slate-100 antialiased">

    <div class="grid h-screen grid-cols-[minmax(290px,340px)_1fr_minmax(290px,340px)]">

        {{-- ==================== KIRI: ORMAS & PARTAI ==================== --}}
        <aside class="flex flex-col overflow-hidden border-r border-cyan-400/20 bg-[#0b1829]">

            {{-- Ormas --}}
            <section class="flex flex-1 flex-col overflow-hidden border-b border-cyan-400/20">
                <div class="border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-cyan-300">Organisasi Masyarakat</h2>
                    </div>
                </div>
                <div class="dashboard-chart" data-chart="ormas"></div>
            </section>

            {{-- Partai --}}
            <section class="flex flex-1 flex-col overflow-hidden">
                <div class="border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-lime-300">Partai Politik</h2>
                    </div>
                </div>
                <div class="dashboard-chart " data-chart="partai"></div>
            </section>

        </aside>

        {{-- ==================== TENGAH: PETA ==================== --}}
        <main class="relative">
            <div id="map" class="h-full w-full"></div>

            <div class="absolute right-4 top-4 z-[1000] w-44 rounded-md border border-cyan-300/30 bg-[#0b1829]/95 p-2 shadow-lg shadow-cyan-950/30 backdrop-blur sm:w-52">
                <p class="mb-1.5 text-[0.65rem] font-semibold uppercase tracking-wider text-slate-300">Pilih Kecamatan</p>
                <div class="grid grid-cols-3 gap-1" data-kecamatan-list>
                    @forelse ($kecamatanList as $kec)
                    <label data-kecamatan-id="{{ $kec->id }}" class="map-kecamatan-button flex min-w-0 cursor-pointer items-center gap-1 rounded px-1 py-1 text-[0.65rem] font-medium text-slate-300 transition hover:bg-cyan-400/20 hover:text-white" title="{{ $kec->nama }}">
                        <input type="radio" name="kecamatan" value="{{ $kec->id }}" class="h-2.5 w-2.5 shrink-0 border-slate-500 bg-slate-800 text-cyan-400 focus:ring-1 focus:ring-cyan-400">
                        <span class="whitespace-nowrap">{{ $kec->kode ?? strtoupper(substr($kec->nama, 0, 3)) }}</span>
                    </label>
                    @empty
                    <span class="col-span-3 text-xs text-slate-500">Belum ada data kecamatan.</span>
                    @endforelse
                </div>
                <div class="mt-2 grid grid-cols-3 gap-1 border-t border-white/10 pt-2">
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-cyan-200">
                        <input type="checkbox" data-layer-toggle="ormas" class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-cyan-400 focus:ring-1 focus:ring-cyan-400">
                        <span>Ormas</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-red-200">
                        <input type="checkbox" data-layer-toggle="partai" class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-red-400 focus:ring-1 focus:ring-red-400">
                        <span>Partai</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-amber-200">
                        <input type="checkbox" data-layer-toggle="agama" class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-amber-400 focus:ring-1 focus:ring-amber-400">
                        <span>Agama</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-green-200">
                        <input type="checkbox" data-layer-toggle="konflik" class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-green-400 focus:ring-1 focus:ring-green-400">
                        <span>Konflik</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-slate-300">
                        <input type="checkbox" data-clear-map class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-slate-300 focus:ring-1 focus:ring-slate-400">
                        <span>Clear</span>
                    </label>
                </div>
            </div>

            {{-- Judul kecil mengambang di tengah-atas peta, identitas dashboard --}}
            <div class="pointer-events-none absolute left-1/2 top-4 z-[1000] -translate-x-1/2 rounded-md border border-cyan-300/30 bg-[#0b1829]/90 px-3 py-1.5 shadow-lg shadow-cyan-950/30 backdrop-blur">
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
                    </div>
                </div>
                <div class="dashboard-chart " data-chart="agama"></div>
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