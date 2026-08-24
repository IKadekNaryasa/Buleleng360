<x-app-layout>
    <div class="dashboard-home flex min-h-[calc(100vh-4rem)] items-center justify-center px-5 py-12 sm:px-8">
        <div class="dashboard-access-panel w-full max-w-3xl rounded-2xl p-8 text-center sm:p-12">
            <span class="dashboard-eyebrow">{{ Auth::user()->role?->name ?? 'User' }} Access</span>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Buleleng 360</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-400">Pusat kendali data dan pemantauan Kabupaten Buleleng.</p>
            @if (Auth::user()->role?->name === 'Administrator')
            <a href="{{ route('admin.dashboard') }}" class="dashboard-console-button mt-8 inline-flex items-center gap-2 rounded-lg px-5 py-3 text-sm font-semibold text-white">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M3 12 12 3l9 9M5 10v10h14V10M9 20v-6h6v6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                </svg>
                Open Admin Console
            </a>
            @elseif (Auth::user()->role?->name === 'Operator')
            <a href="{{ route('operator.dashboard') }}" class="dashboard-console-button mt-8 inline-flex items-center gap-2 rounded-lg px-5 py-3 text-sm font-semibold text-white">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                </svg>
                Open Operator Console
            </a>
            @endif
        </div>
    </div>
</x-app-layout>