@extends('layouts.admin')
@section('content')
<div class="admin-panel mx-auto max-w-4xl">
    <div class="border-b border-white/10 p-5">
        <p class="text-sm text-slate-400">{{ $item ? 'Perbarui data' : 'Data baru' }}</p>
        <h2 class="text-xl font-semibold text-white">{{ $item ? 'Edit' : 'Tambah' }} {{ $config['title'] }}</h2>
    </div>
    <form method="POST" action="{{ $item ? route($routePrefix.'.resource.update', [$resource, $item->id]) : route($routePrefix.'.resource.store', $resource) }}" class="grid gap-5 p-5 sm:grid-cols-2">@csrf @if ($item) @method('PUT') @endif
        @foreach ($config['fields'] as $field) @php [$name, $label, $type] = $field; $optionKey = $field[3] ?? null; $value = old($name, data_get($item, $name)); @endphp
        <div class="{{ in_array($type, ['textarea', 'json']) ? 'sm:col-span-2' : '' }}"><label for="{{ $name }}" class="admin-label">{{ $label }}</label>
            @if ($type === 'select' && $optionKey === 'desas')
            @php $selectedDesaLabel = $value ? $options['desas']->get($value) : ''; @endphp
            <div class="admin-search-select" data-search-select>
                <input id="{{ $name }}" type="search" value="{{ $selectedDesaLabel }}" class="admin-input" placeholder="Ketik nama desa..." autocomplete="off" required data-search-input>
                <input type="hidden" name="{{ $name }}" value="{{ $value }}" required data-search-value>
                <div class="admin-search-options" data-search-options>
                    @foreach ($options['desas'] as $optionValue => $optionLabel)
                    <button type="button" class="admin-search-option" data-search-option data-value="{{ $optionValue }}" data-label="{{ $optionLabel }}">{{ $optionLabel }}</button>
                    @endforeach
                </div>
            </div>
            @elseif ($type === 'select' || $type === 'select-agama')<select id="{{ $name }}" name="{{ $name }}" class="admin-input" required>
                <option value="">Pilih {{ $label }}</option>@foreach (($type === 'select-agama' ? ['islam'=>'Islam','kristen_protestan'=>'Kristen Protestan','kristen_katolik'=>'Kristen Katolik','hindu'=>'Hindu','buddha'=>'Buddha','khonghucu'=>'Khonghucu','lainnya'=>'Lainnya'] : $options[$optionKey]) as $optionValue => $optionLabel)<option value="{{ $optionValue }}" @selected($value==$optionValue)>{{ $optionLabel }}</option>@endforeach
            </select>
            @elseif ($type === 'textarea' || $type === 'json')<textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $type === 'json' ? 5 : 3 }}" class="admin-input" {{ $type === 'json' || $name === 'alamat' ? '' : 'required' }}>{{ $type === 'json' && is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value }}</textarea>
            @else<input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : $value }}" class="admin-input" {{ $type !== 'password' || !$item ? 'required' : '' }} step="any">@endif
            @error($name)<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
        </div>@endforeach
        <div class="flex gap-3 sm:col-span-2"><a href="{{ route($routePrefix.'.resource.index', $resource) }}" class="admin-button"><svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m6 6 12 12M18 6 6 18" />
                </svg>Batal</a><button class="admin-button-primary"><svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 4h12l2 2v14H5V4Z" />
                    <path d="M8 4v6h8V4M8 20v-6h8v6" />
                </svg>Simpan</button></div>
    </form>
</div>
@endsection