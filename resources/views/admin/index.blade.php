@extends('layouts.admin')
@section('content')
<div class="admin-panel mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 p-5">
        <div>
            <p class="text-sm text-slate-400">Data master</p>
            <h2 class="text-xl font-semibold text-white">{{ $config['title'] }}</h2>
        </div><a href="{{ route($routePrefix.'.resource.create', $resource) }}" class="admin-button-primary"><svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 5v14M5 12h14" />
            </svg>Tambah {{ $config['title'] }}</a>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No.</th>@foreach ($config['columns'] as $column)<th>{{ str($column)->replace('.', ' ')->replace('_', ' ')->title() }}</th>@endforeach<th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)<tr>
                    <td>{{ $items->firstItem() + $loop->index }}</td>@foreach ($config['columns'] as $column)<td>{{ data_get($item, $column, '-') }}</td>@endforeach<td>
                        <div class="flex justify-end gap-2"><a href="{{ route($routePrefix.'.resource.edit', [$resource, $item->id]) }}" class="admin-action admin-action-edit" title="Update data"><svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20 11a8 8 0 0 0-14.9-3M4 13a8 8 0 0 0 14.9 3" />
                                    <path d="M5 4v4h4M19 20v-4h-4" />
                                </svg><span>Update</span></a>
                            <form method="POST" action="{{ route($routePrefix.'.resource.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="admin-action admin-action-delete" title="Hapus data"><svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" />
                                    </svg><span>Hapus</span></button></form>
                        </div>
                    </td>
                </tr>@empty<tr>
                    <td colspan="{{ count($config['columns']) + 2 }}" class="text-center text-slate-400">Belum ada data.</td>
                </tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-white/10 p-5">{{ $items->links() }}</div>
</div>
@endsection