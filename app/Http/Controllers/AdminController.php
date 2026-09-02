<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use App\Models\Bidang;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Ormas;
use App\Models\Partai;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\SebaranAgama;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const ADMIN_RESOURCES = ['users', 'bidang', 'desa', 'kecamatan'];

    private const OPERATOR_RESOURCES = ['partai', 'ormas', 'penduduk', 'agama', 'sebaran-agama'];

    private const RESOURCES = [
        'users' => ['title' => 'User', 'model' => User::class, 'columns' => ['name', 'nip', 'email', 'role.name'], 'fields' => [['name', 'Nama', 'text'], ['nip', 'NIP', 'text'], ['email', 'Email', 'email'], ['role_id', 'Role', 'select', 'roles'], ['bidang_id', 'Bidang', 'select', 'bidangs'], ['password', 'Password', 'password']]],
        'bidang' => ['title' => 'Bidang', 'model' => Bidang::class, 'columns' => ['name'], 'fields' => [['name', 'Nama Bidang', 'text']]],
        'kecamatan' => ['title' => 'Kecamatan', 'model' => Kecamatan::class, 'columns' => ['nama', 'lat', 'long'], 'fields' => [['nama', 'Nama', 'text'], ['lat', 'Latitude', 'number'], ['long', 'Longitude', 'number'], ['geojson_boundary', 'GeoJSON Boundary', 'json']]],
        'desa' => ['title' => 'Desa', 'model' => Desa::class, 'columns' => ['nama', 'kecamatan.nama', 'lat', 'long'], 'fields' => [['kecamatan_id', 'Kecamatan', 'select', 'kecamatans'], ['nama', 'Nama', 'text'], ['lat', 'Latitude', 'number'], ['long', 'Longitude', 'number'], ['geojson_boundary', 'GeoJSON Boundary', 'json']]],
        'partai' => ['title' => 'Partai', 'model' => Partai::class, 'columns' => ['nama', 'desa.nama', 'jumlah_kader', 'ketua'], 'fields' => [['desa_id', 'Desa', 'select', 'desas'], ['nama', 'Nama Partai', 'text'], ['jumlah_kader', 'Jumlah Kader', 'number'], ['ketua', 'Ketua', 'text'], ['sekretaris', 'Sekretaris', 'text'], ['bendahara', 'Bendahara', 'text'], ['lat', 'Latitude', 'number'], ['long', 'Longitude', 'number'], ['alamat', 'Alamat', 'textarea']]],
        'ormas' => ['title' => 'Ormas', 'model' => Ormas::class, 'columns' => ['nama', 'desa.nama', 'jumlah_anggota', 'ketua'], 'fields' => [['desa_id', 'Desa', 'select', 'desas'], ['nama', 'Nama Ormas', 'text'], ['jumlah_anggota', 'Jumlah Anggota', 'number'], ['ketua', 'Ketua', 'text'], ['sekretaris', 'Sekretaris', 'text'], ['bendahara', 'Bendahara', 'text'], ['lat', 'Latitude', 'number'], ['long', 'Longitude', 'number'], ['alamat', 'Alamat', 'textarea']]],
        'agama' => ['title' => 'Agama', 'model' => Agama::class, 'columns' => ['agama'], 'fields' => [['agama', 'Agama', 'select-agama']]],
        'penduduk' => ['title' => 'Penduduk', 'model' => Penduduk::class, 'columns' => ['desa.nama', 'total_jiwa', 'tahun'], 'fields' => [['desa_id', 'Desa', 'select', 'desas'], ['total_jiwa', 'Total Jiwa', 'number'], ['tahun', 'Tahun', 'number']]],
        'sebaran-agama' => ['title' => 'Sebaran Agama', 'model' => SebaranAgama::class, 'columns' => ['desa.nama', 'agama.agama', 'jumlah_pemeluk'], 'fields' => [['desa_id', 'Desa', 'select', 'desas'], ['agama_id', 'Agama', 'select', 'agamas'], ['jumlah_pemeluk', 'Jumlah Pemeluk', 'number']]],
    ];

    public function dashboard(): View
    {
        return $this->dashboardView(self::ADMIN_RESOURCES);
    }

    public function operatorDashboard(): View
    {
        return $this->dashboardView(self::OPERATOR_RESOURCES);
    }

    private function dashboardView(array $resources): View
    {
        $counts = [];

        foreach ($resources as $resource) {
            $config = self::RESOURCES[$resource];
            $counts[$resource] = $config['model']::count();
        }

        return view('admin.dashboard', ['counts' => $counts, 'resources' => $resources, 'routePrefix' => $this->routePrefix()]);
    }

    public function index(string $resource): View
    {
        $config = $this->resourceConfig($resource);
        $items = $config['model']::query()->with($this->relationsFor($resource))->paginate(12);

        return view('admin.index', ['resource' => $resource, 'config' => $config, 'items' => $items, 'routePrefix' => $this->routePrefix()]);
    }

    public function create(string $resource): View
    {
        $config = $this->resourceConfig($resource);

        return view('admin.form', ['resource' => $resource, 'config' => $config, 'item' => null, 'options' => $this->options(), 'routePrefix' => $this->routePrefix()]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        $data = $this->validatedData($request, $resource);
        $config['model']::create($data);

        return to_route($this->routePrefix().'.resource.index', $resource)->with('status', $config['title'].' berhasil ditambahkan.');
    }

    public function edit(string $resource, string $id): View
    {
        $config = $this->resourceConfig($resource);
        $item = $config['model']::findOrFail($id);

        return view('admin.form', ['resource' => $resource, 'config' => $config, 'item' => $item, 'options' => $this->options(), 'routePrefix' => $this->routePrefix()]);
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        $item = $config['model']::findOrFail($id);
        $item->update($this->validatedData($request, $resource, $item));

        return to_route($this->routePrefix().'.resource.index', $resource)->with('status', $config['title'].' berhasil diperbarui.');
    }

    public function destroy(string $resource, string $id): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        $config['model']::findOrFail($id)->delete();

        return to_route($this->routePrefix().'.resource.index', $resource)->with('status', $config['title'].' berhasil dihapus.');
    }

    private function resourceConfig(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);

        return self::RESOURCES[$resource];
    }

    private function routePrefix(): string
    {
        return request()->routeIs('operator.*') ? 'operator' : 'admin';
    }

    private function relationsFor(string $resource): array
    {
        return match ($resource) {
            'users' => ['role', 'bidang'],
            'desa' => ['kecamatan'],
            'partai', 'ormas', 'penduduk', 'sebaran-agama' => ['desa'],
            default => [],
        };
    }

    private function options(): array
    {
        return [
            'roles' => Role::orderBy('name')->pluck('name', 'id'),
            'bidangs' => Bidang::orderBy('name')->pluck('name', 'id'),
            'kecamatans' => Kecamatan::orderBy('nama')->pluck('nama', 'id'),
            'desas' => Desa::with('kecamatan')->orderBy('nama')->get()->mapWithKeys(fn (Desa $desa): array => [$desa->id => $desa->nama.' - '.$desa->kecamatan?->nama]),
            'agamas' => Agama::orderBy('agama')->pluck('agama', 'id'),
        ];
    }

    private function validatedData(Request $request, string $resource, ?object $item = null): array
    {
        $rules = match ($resource) {
            'users' => ['name' => ['required', 'string', 'max:255'], 'nip' => ['required', 'string', 'max:255', Rule::unique('users', 'nip')->ignore($item?->id)], 'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($item?->id)], 'role_id' => ['required', 'exists:roles,id'], 'bidang_id' => ['required', 'exists:bidangs,id'], 'password' => [$item ? 'nullable' : 'required', 'string', 'min:8']],
            'bidang' => ['name' => ['required', 'string', 'max:150', Rule::unique('bidangs', 'name')->ignore($item?->id)]],
            'kecamatan' => ['nama' => ['required', 'string', 'max:100'], 'lat' => ['required', 'numeric', 'between:-90,90'], 'long' => ['required', 'numeric', 'between:-180,180'], 'geojson_boundary' => ['nullable', 'json']],
            'desa' => ['kecamatan_id' => ['required', 'exists:kecamatans,id'], 'nama' => ['required', 'string', 'max:100'], 'lat' => ['required', 'numeric', 'between:-90,90'], 'long' => ['required', 'numeric', 'between:-180,180'], 'geojson_boundary' => ['nullable', 'json']],
            'partai' => $this->organizationRules('partai'),
            'ormas' => $this->organizationRules('ormas'),
            'agama' => ['agama' => ['required', Rule::in(['islam', 'kristen_protestan', 'kristen_katolik', 'hindu', 'buddha', 'khonghucu', 'lainnya'])]],
            'penduduk' => ['desa_id' => ['required', 'exists:desas,id'], 'total_jiwa' => ['required', 'integer', 'min:0'], 'tahun' => ['required', 'integer', 'between:1900,2200', Rule::unique('penduduks')->where(fn ($query) => $query->where('desa_id', $request->input('desa_id')))->ignore($item?->id)]],
            'sebaran-agama' => ['desa_id' => ['required', 'exists:desas,id'], 'agama_id' => ['required', 'exists:agamas,id'], 'jumlah_pemeluk' => ['required', 'integer', 'min:0']],
        };

        $data = Validator::make($request->all(), $rules)->validate();

        if (in_array($resource, ['desa', 'kecamatan'], true)) {
            $incomingGeojson = $data['geojson_boundary'] ?? null;
            $isGeojsonBlank = $this->isBlankGeojsonBoundary($incomingGeojson);

            if ($isGeojsonBlank && $item?->geojson_boundary) {
                $data['geojson_boundary'] = $item->geojson_boundary;
            }

            if (! $isGeojsonBlank && is_string($incomingGeojson)) {
                $decoded = json_decode($incomingGeojson, true);

                if (is_array($decoded) && isset($decoded['type'], $decoded['coordinates'])) {
                    $data['geojson_boundary'] = $decoded;
                }
            }
        }

        if ($resource === 'users' && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif ($resource === 'users') {
            unset($data['password']);
        }

        return $data;
    }

    private function isBlankGeojsonBoundary(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '' || strtolower($trimmed) === 'null') {
                return true;
            }

            $decoded = json_decode($trimmed, true);

            if ($decoded === null && $trimmed !== 'null') {
                return false;
            }

            return $decoded === null || count($decoded ?? []) === 0;
        }

        if (is_array($value)) {
            return count($value) === 0 || in_array(null, $value, true) || in_array('', $value, true) || in_array('null', $value, true);
        }

        return false;
    }

    private function organizationRules(string $resource): array
    {
        return ['desa_id' => ['required', 'exists:desas,id'], 'nama' => ['required', 'string', 'max:150'], $resource === 'ormas' ? 'jumlah_anggota' : 'jumlah_kader' => ['required', 'integer', 'min:0'], 'ketua' => ['required', 'string', 'max:150'], 'sekretaris' => ['required', 'string', 'max:150'], 'bendahara' => ['required', 'string', 'max:150'], 'lat' => ['required', 'numeric', 'between:-90,90'], 'long' => ['required', 'numeric', 'between:-180,180'], 'alamat' => ['nullable', 'string']];
    }
}
