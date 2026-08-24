<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} | Buleleng 360</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
</head>

<body class="tech-shell min-h-screen text-slate-100 antialiased">
    @php
    $isOperator = Auth::user()->role?->name === 'Operator';
    $routePrefix = $isOperator ? 'operator' : 'admin';
    $navigation = $isOperator
    ? ['partai' => 'Partai', 'ormas' => 'Ormas', 'penduduk' => 'Penduduk', 'agama' => 'Agama', 'sebaran-agama' => 'Sebaran Agama']
    : ['users' => 'User', 'bidang' => 'Bidang', 'kecamatan' => 'Kecamatan', 'desa' => 'Desa'];
    @endphp
    <div class="mx-auto flex min-h-screen max-w-[1600px]">
        <aside class="hidden w-64 shrink-0 border-r border-cyan-400/15 bg-slate-950/70 p-5 lg:block">
            <a href="{{ route($routePrefix.'.dashboard') }}" class="block border-b border-cyan-400/15 pb-5">
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-cyan-300">Buleleng 360</span>
                <strong class="mt-2 block text-xl text-white">{{ $isOperator ? 'Operator Console' : 'Admin Console' }}</strong>
            </a>
            <nav class="mt-5 space-y-1">
                <a href="{{ route($routePrefix.'.dashboard') }}" class="admin-nav-link {{ request()->routeIs($routePrefix.'.dashboard') ? 'admin-nav-active' : '' }}" @if (request()->routeIs($routePrefix.'.dashboard')) aria-current="page" @endif>Overview</a>
                @foreach ($navigation as $key => $label)
                <a href="{{ route($routePrefix.'.resource.index', $key) }}" class="admin-nav-link {{ request()->route('resource') === $key ? 'admin-nav-active' : '' }}" @if (request()->route('resource') === $key) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button class="admin-nav-link w-full text-left text-rose-300">Keluar</button>
            </form>
        </aside>
        <main class="min-w-0 flex-1 p-5 sm:p-8">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-cyan-300">{{ $isOperator ? 'Operator' : 'Administrator' }}</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">{{ $heading ?? ($isOperator ? 'Operator Console' : 'Admin Console') }}</h1>
                </div>
                <a href="{{ url('/') }}" class="admin-button"><svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m3 12 9-9 9 9M5 10v10h14V10M9 20v-6h6v6" />
                    </svg>Lihat Dashboard</a>
            </div>
            @if (session('status')) <div class="mb-5 rounded-lg border border-emerald-400/25 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div> @endif
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>

</html>