<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buleleng 360 — Dashboard Pemantauan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
</head>

<body class="dashboard-shell h-screen w-screen overflow-hidden bg-[#07111f] text-slate-100 antialiased">

    <div class="dashboard-layout grid grid-cols-[minmax(330px,380px)_1fr_minmax(330px,380px)] gap-3 p-3">

        {{-- ==================== KIRI: ORMAS & PARTAI ==================== --}}
        <aside class="dashboard-sidebar flex flex-col gap-3 overflow-hidden bg-transparent">

            {{-- Ormas --}}
            <section class="dashboard-section dashboard-card-emerald flex flex-1 flex-col overflow-hidden">
                <div class="dashboard-section-header border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-cyan-300">Organisasi Masyarakat</h2>
                    </div>
                </div>
                <div class="dashboard-chart dashboard-chart-panel" data-chart="ormas"></div>
            </section>

            {{-- Partai --}}
            <section class="dashboard-section dashboard-card-red flex flex-1 flex-col overflow-hidden">
                <div class="dashboard-section-header border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-lime-300">Partai Politik</h2>
                    </div>
                </div>
                <div class="dashboard-chart dashboard-chart-panel" data-chart="partai"></div>
            </section>

        </aside>

        {{-- ==================== TENGAH: PETA ==================== --}}
        <main class="dashboard-map-stage relative overflow-hidden rounded-xl border border-cyan-300/25">
            <div id="map" class="h-full w-full"></div>

            <div class="dashboard-status-bar absolute left-16 top-4 z-[1000] flex items-center gap-2">
                <span class="dashboard-status-title">Buleleng 360</span>
                <span class="dashboard-status-chip dashboard-status-live">● LIVE DATA</span>
                <span class="dashboard-status-chip">MAP VIEW</span>
            </div>

            <div class="dashboard-map-controls absolute right-4 top-4 z-[1000] w-44 rounded-md border border-cyan-300/30 bg-[#0b1829]/95 p-2 shadow-lg shadow-cyan-950/30 backdrop-blur sm:w-52">
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
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-sky-200">
                        <input type="checkbox" data-boundary-toggle="desa" checked class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-sky-400 focus:ring-1 focus:ring-sky-400">
                        <span>Desa</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-1 text-[0.6rem] text-emerald-200">
                        <input type="checkbox" data-boundary-toggle="kecamatan" checked class="h-2.5 w-2.5 rounded border-slate-500 bg-slate-800 text-emerald-400 focus:ring-1 focus:ring-emerald-400">
                        <span>Kecamatan</span>
                    </label>
                </div>
            </div>

        </main>

        {{-- ==================== KANAN: AGAMA & KONFLIK ==================== --}}
        <aside class="dashboard-sidebar flex flex-col gap-3 overflow-hidden bg-transparent">

            {{-- Agama --}}
            <section class="dashboard-section dashboard-card-amber flex flex-1 flex-col overflow-hidden">
                <div class="dashboard-section-header border-b border-white/10 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-amber-300">Sebaran Agama</h2>
                    </div>
                </div>
                <div class="dashboard-chart dashboard-chart-panel" data-chart="agama"></div>
            </section>

            {{-- Konflik: sengaja dikosongkan dulu --}}
            <section class="dashboard-section dashboard-card-slate flex flex-1 flex-col overflow-hidden">
                <div class="dashboard-section-header border-b border-slate-100 px-4 py-3">
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