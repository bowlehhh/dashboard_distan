<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $district = $request->string('district')->toString();
        $type = $request->string('type')->toString();
        $alsintans = Alsintan::query()->when($district, fn ($query) => $query->where('district', $district));

        $recentData = collect()
            ->merge($this->whenType($type, 'Poktan', fn () => Poktan::query()
                ->where('status', 'Aktif')
                ->latest()
                ->take(2)
                ->get()
                ->map(fn (Poktan $item): array => [
                    'type' => 'Poktan',
                    'name' => $item->name,
                    'district' => $item->district,
                    'village' => $item->village,
                    'poktan' => '—',
                    'commodity' => $item->commodity,
                    'columns' => [
                        'Ketua' => $item->chairperson,
                        'Anggota' => $item->member_count.' orang',
                    ],
                    'coordinates' => '—',
                    'status' => $item->status,
                    'photo' => null,
                    'recorded_at' => $item->created_at,
                    'show_url' => auth()->user()->hasRole('admin', 'operator') ? route('poktans.show', $item) : null,
                    'edit_url' => auth()->user()->hasRole('admin', 'operator') ? route('poktans.edit', $item) : null,
                ])))
            ->merge($this->whenType($type, 'Saprodi', fn () => Saprodi::query()
                ->latest()
                ->take(2)
                ->get()
                ->map(fn (Saprodi $item): array => [
                    'type' => 'Saprodi',
                    'name' => $item->name,
                    'district' => '—',
                    'village' => '—',
                    'poktan' => '—',
                    'commodity' => '—',
                    'columns' => [
                        'Kategori' => $item->category,
                        'Stok' => number_format((float) $item->stock, 2, ',', '.'),
                        'Satuan' => $item->unit,
                        'Batas Minimum' => (float) $item->minimum_stock > 0 ? number_format((float) $item->minimum_stock, 2, ',', '.') : null,
                    ],
                    'coordinates' => '—',
                    'status' => $item->stock_status,
                    'photo' => $item->photo_path ? asset('storage/'.$item->photo_path) : null,
                    'recorded_at' => $item->created_at,
                    'show_url' => auth()->user()->hasRole('admin', 'operator') ? route('saprodis.show', $item) : null,
                    'edit_url' => auth()->user()->hasRole('admin', 'operator') ? route('saprodis.edit', $item) : null,
                ])))
            ->merge($this->whenType($type, 'Tanaman Pangan', fn () => Crop::with('poktan')
                ->latest()
                ->take(2)
                ->get()
                ->map(fn (Crop $item): array => [
                    'type' => 'Tanaman Pangan',
                    'name' => $item->commodity,
                    'district' => $item->district,
                    'village' => $item->poktan?->village ?? '—',
                    'poktan' => $item->poktan?->name ?? '—',
                    'commodity' => $item->commodity,
                    'columns' => [
                        'Luas Tanam' => rtrim(rtrim(number_format((float) $item->planted_area, 2, ',', '.'), '0'), ',').' Ha',
                        'Luas Panen' => rtrim(rtrim(number_format((float) $item->harvested_area, 2, ',', '.'), '0'), ',').' Ha',
                        'Produksi' => rtrim(rtrim(number_format((float) $item->production, 2, ',', '.'), '0'), ','),
                        'Satuan' => $item->unit,
                        'Periode' => $item->period,
                        'Keterangan' => $item->notes,
                    ],
                    'coordinates' => '—',
                    'status' => $item->period,
                    'photo' => null,
                    'recorded_at' => $item->created_at,
                    'show_url' => auth()->user()->hasRole('admin', 'operator') ? route('crops.show', $item) : null,
                    'edit_url' => auth()->user()->hasRole('admin', 'operator') ? route('crops.edit', $item) : null,
                ])))
            ->merge($this->whenType($type, 'Alsintan', fn () => (clone $alsintans)
                ->with('poktan')
                ->latest()
                ->take(2)
                ->get()
                ->map(fn (Alsintan $item): array => [
                    'type' => 'Alsintan',
                    'name' => $item->type,
                    'district' => $item->district,
                    'village' => $item->village,
                    'poktan' => $item->poktan?->name ?? '—',
                    'commodity' => $item->poktan?->commodity ?? '—',
                    'columns' => [
                        'Merek / Tipe' => $item->brand_type,
                        'No. Inventaris' => $item->inventory_number,
                        'Tahun' => (string) $item->procurement_year,
                        'Penggunaan' => $item->usage_status,
                    ],
                    'coordinates' => $item->latitude !== null && $item->longitude !== null ? 'Lat: '.$item->latitude.' | Long: '.$item->longitude : '—',
                    'status' => $item->condition,
                    'photo' => $item->photo_path ? asset('storage/'.$item->photo_path) : null,
                    'recorded_at' => $item->created_at,
                    'show_url' => route('alsintans.show', $item),
                    'edit_url' => auth()->user()->hasRole('admin', 'operator') ? route('alsintans.edit', $item) : null,
                ])))
            ->sortByDesc(fn (array $item) => $item['recorded_at'])
            ->take(8)
            ->values();

        // Ordered table columns: the label is the header, cells render the matching value.
        $tableColumns = match ($type) {
            'Poktan' => [
                ['key' => 'no', 'label' => 'No'],
                ['key' => 'name', 'label' => 'Nama Poktan'],
                ['key' => 'district', 'label' => 'Kecamatan'],
                ['key' => 'village', 'label' => 'Kampung / Desa'],
                ['key' => 'field', 'field' => 'Ketua', 'label' => 'Ketua'],
                ['key' => 'field', 'field' => 'Anggota', 'label' => 'Anggota'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'photo', 'label' => 'Foto'],
                ['key' => 'actions', 'label' => 'Aksi'],
            ],
            'Saprodi' => [
                ['key' => 'no', 'label' => 'No'],
                ['key' => 'name', 'label' => 'Nama Saprodi'],
                ['key' => 'field', 'field' => 'Kategori', 'label' => 'Kategori'],
                ['key' => 'field', 'field' => 'Stok', 'label' => 'Stok'],
                ['key' => 'field', 'field' => 'Satuan', 'label' => 'Satuan'],
                ['key' => 'field', 'field' => 'Batas Minimum', 'label' => 'Batas Minimum'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'photo', 'label' => 'Foto'],
                ['key' => 'actions', 'label' => 'Aksi'],
            ],
            'Tanaman Pangan' => [
                ['key' => 'no', 'label' => 'No'],
                ['key' => 'name', 'label' => 'Komoditas'],
                ['key' => 'district', 'label' => 'Kecamatan'],
                ['key' => 'village', 'label' => 'Kampung / Desa'],
                ['key' => 'poktan', 'label' => 'Kelompok Tani'],
                ['key' => 'field', 'field' => 'Luas Tanam', 'label' => 'Luas Tanam'],
                ['key' => 'field', 'field' => 'Luas Panen', 'label' => 'Luas Panen'],
                ['key' => 'field', 'field' => 'Produksi', 'label' => 'Produksi'],
                ['key' => 'field', 'field' => 'Satuan', 'label' => 'Satuan'],
                ['key' => 'field', 'field' => 'Periode', 'label' => 'Periode'],
                ['key' => 'field', 'field' => 'Keterangan', 'label' => 'Keterangan'],
                ['key' => 'photo', 'label' => 'Foto'],
                ['key' => 'actions', 'label' => 'Aksi'],
            ],
            'Alsintan' => [
                ['key' => 'no', 'label' => 'No'],
                ['key' => 'name', 'label' => 'Jenis Alsintan'],
                ['key' => 'district', 'label' => 'Kecamatan'],
                ['key' => 'village', 'label' => 'Kampung / Desa'],
                ['key' => 'poktan', 'label' => 'Poktan / Pemilik'],
                ['key' => 'field', 'field' => 'Merek / Tipe', 'label' => 'Merek / Tipe'],
                ['key' => 'field', 'field' => 'No. Inventaris', 'label' => 'No. Inventaris'],
                ['key' => 'field', 'field' => 'Tahun', 'label' => 'Tahun'],
                ['key' => 'field', 'field' => 'Penggunaan', 'label' => 'Penggunaan'],
                ['key' => 'status', 'label' => 'Kondisi'],
                ['key' => 'coordinates', 'label' => 'Koordinat'],
                ['key' => 'photo', 'label' => 'Foto'],
                ['key' => 'actions', 'label' => 'Aksi'],
            ],
            default => [
                ['key' => 'no', 'label' => 'No'],
                ['key' => 'type', 'label' => 'Jenis Data'],
                ['key' => 'name', 'label' => 'Nama / Item'],
                ['key' => 'district', 'label' => 'Kecamatan'],
                ['key' => 'village', 'label' => 'Kampung / Desa'],
                ['key' => 'poktan', 'label' => 'Poktan'],
                ['key' => 'commodity', 'label' => 'Tanaman / Komoditas'],
                ['key' => 'coordinates', 'label' => 'Koordinat'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'photo', 'label' => 'Foto'],
                ['key' => 'actions', 'label' => 'Aksi'],
            ],
        };

        return view('dashboard', [
            'districts' => Alsintan::query()->distinct()->orderBy('district')->pluck('district'),
            'selectedDistrict' => $district,
            'selectedType' => $type,
            'typeOptions' => ['Poktan', 'Saprodi', 'Tanaman Pangan', 'Alsintan'],
            'tableColumns' => $tableColumns,
            'stats' => [
                ['label' => 'Total Poktan', 'value' => Poktan::query()->where('status', 'Aktif')->count(), 'caption' => 'Kelompok tani aktif', 'icon' => '◉'],
                ['label' => 'Total Alsintan', 'value' => (clone $alsintans)->count(), 'caption' => 'Unit terdaftar', 'icon' => '▣'],
                ['label' => 'Total Saprodi', 'value' => Saprodi::count(), 'caption' => 'Jenis saprodi', 'icon' => '◇'],
                ['label' => 'Tanaman Pangan', 'value' => Crop::distinct('commodity')->count('commodity'), 'caption' => 'Komoditas', 'icon' => '◆'],
            ],
            'districtData' => (clone $alsintans)->selectRaw('district, count(*) as total')->groupBy('district')->orderByDesc('total')->get(),
            'conditionData' => (clone $alsintans)->select('condition')->selectRaw('count(*) as total')->groupBy('condition')->get()->keyBy('condition'),
            'recentData' => $recentData,
        ]);
    }

    /**
     * Only fetch and map the given data type when it is not filtered out.
     *
     * @param  callable(): iterable<int, array<string, mixed>>  $map
     * @return array<int, array<string, mixed>>
     */
    private function whenType(string $type, string $wanted, callable $map): array
    {
        if ($type !== '' && $type !== $wanted) {
            return [];
        }

        return collect($map())->all();
    }
}
