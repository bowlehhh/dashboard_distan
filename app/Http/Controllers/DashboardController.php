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
        $alsintans = Alsintan::query()->when($district, fn ($query) => $query->where('district', $district));

        $recentData = collect()
            ->merge(Poktan::query()->where('status', 'Aktif')->latest()->take(2)->get()->map(fn (Poktan $item): array => ['category' => 'Poktan', 'name' => $item->name, 'detail_label' => 'Ketua', 'detail' => $item->chairperson, 'district' => $item->district, 'village' => $item->village, 'poktan' => '—', 'commodity' => $item->commodity, 'coordinates' => '—', 'info' => $item->member_count.' anggota', 'status' => $item->status, 'photo' => null, 'recorded_at' => $item->created_at]))
            ->merge(Saprodi::query()->latest()->take(2)->get()->map(fn (Saprodi $item): array => ['category' => 'Saprodi', 'name' => $item->name, 'detail_label' => 'Kategori', 'detail' => $item->category, 'district' => '—', 'village' => '—', 'poktan' => '—', 'commodity' => '—', 'coordinates' => '—', 'info' => number_format((float) $item->stock, 2, ',', '.').' '.$item->unit, 'status' => $item->stock_status, 'photo' => $item->photo_path ? asset('storage/'.$item->photo_path) : null, 'recorded_at' => $item->created_at]))
            ->merge(Crop::with('poktan')->latest()->take(2)->get()->map(fn (Crop $item): array => ['category' => 'Tanaman Pangan', 'name' => $item->commodity, 'detail_label' => 'Satuan', 'detail' => $item->unit, 'district' => $item->district, 'village' => $item->poktan?->village ?? '—', 'poktan' => $item->poktan?->name ?? '—', 'commodity' => $item->commodity, 'coordinates' => '—', 'info' => $item->production.' '.$item->unit, 'status' => $item->period, 'photo' => null, 'recorded_at' => $item->created_at]))
            ->merge((clone $alsintans)->with('poktan')->latest()->take(2)->get()->map(fn (Alsintan $item): array => ['category' => 'Alsintan', 'name' => $item->type, 'detail_label' => 'Merek / Tipe', 'detail' => $item->brand_type, 'district' => $item->district, 'village' => $item->village, 'poktan' => $item->poktan?->name ?? '—', 'commodity' => $item->poktan?->commodity ?? '—', 'coordinates' => $item->latitude !== null && $item->longitude !== null ? 'Lat: '.$item->latitude.' | Long: '.$item->longitude : '—', 'info' => (string) $item->procurement_year, 'status' => $item->condition, 'photo' => $item->photo_path ? asset('storage/'.$item->photo_path) : null, 'recorded_at' => $item->created_at]))
            ->sortByDesc(fn (array $item) => $item['recorded_at'])
            ->take(8)
            ->values();

        return view('dashboard', [
            'districts' => Alsintan::query()->distinct()->orderBy('district')->pluck('district'),
            'selectedDistrict' => $district,
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
}
