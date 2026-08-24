@extends('layouts.admin')
@section('content')
<div class="mb-6 max-w-2xl text-sm leading-6 text-slate-400">Kelola data Buleleng 360 dengan alur yang ringkas. Pilih modul untuk melihat, menambah, atau memperbarui data.</div>
@php $labels = ['users' => 'User', 'bidang' => 'Bidang', 'kecamatan' => 'Kecamatan', 'desa' => 'Desa', 'partai' => 'Partai', 'ormas' => 'Ormas', 'agama' => 'Agama', 'penduduk' => 'Penduduk', 'sebaran-agama' => 'Sebaran Agama']; @endphp
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($resources as $key)
    @php $label = $labels[$key]; @endphp
    <a href="{{ route($routePrefix.'.resource.index', $key) }}" class="admin-stat group"><span>{{ $label }}</span><strong>{{ number_format($counts[$key], 0, ',', '.') }}</strong><small>Kelola data <span aria-hidden="true">→</span></small></a>
    @endforeach
</div>
@endsection