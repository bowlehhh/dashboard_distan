<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;

class PublicInformationController extends Controller
{
    public function poktans(): View
    {
        $activePoktans = Poktan::query()->where('status', 'Aktif');
        $poktanTotal = (clone $activePoktans)->count();

        $rows = (clone $activePoktans)
            ->select(['name', 'district', 'commodity', 'member_count', 'status'])
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(fn (Poktan $poktan): array => [
                $poktan->name,
                $poktan->district,
                $poktan->commodity,
                number_format($poktan->member_count).' anggota',
                $poktan->status,
            ])
            ->all();

        return $this->page(
            title: 'Data Kelompok Tani',
            description: 'Direktori kelompok tani aktif di Kabupaten Kutai Barat untuk layanan publik dan pemantauan.',
            statistic: $poktanTotal,
            statisticLabel: 'Kelompok tani terdaftar',
            columns: ['Kelompok Tani', 'Kecamatan', 'Komoditas', 'Anggota', 'Status'],
            rows: $rows,
        );
    }

    public function alsintans(): View
    {
        $alsintanTotal = Alsintan::query()->count();

        $rows = Alsintan::query()
            ->select(['type', 'brand_type', 'district', 'procurement_year'])
            ->orderBy('type')
            ->paginate(4);

        $rows->getCollection()->transform(fn (Alsintan $alsintan): array => [
            $alsintan->type,
            $alsintan->brand_type,
            $alsintan->district,
            $alsintan->procurement_year,
        ]);

        return $this->page(
            title: 'Data Alsintan',
            description: 'Ringkasan alat dan mesin pertanian untuk mendukung kegiatan budidaya di Kutai Barat.',
            statistic: $alsintanTotal,
            statisticLabel: 'Unit alsintan terdaftar',
            columns: ['Jenis Alsintan', 'Merk / Tipe', 'Kecamatan', 'Tahun Diserahkan'],
            rows: $rows,
        );
    }

    public function saprodis(): View
    {
        $saprodiTotal = Saprodi::query()->count();
        $rows = Saprodi::query()
            ->with('poktan:id,name')
            ->select(['name', 'poktan_id', 'quantity_distributed', 'unit', 'distributed_year'])
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(fn (Saprodi $saprodi): array => [
                $saprodi->name,
                $saprodi->poktan?->name ?? '—',
                number_format((float) $saprodi->quantity_distributed, 0, ',', '.'),
                $saprodi->unit,
                $saprodi->distributed_year ?? '—',
            ])
            ->all();

        return $this->page(
            title: 'Data Saprodi',
            description: 'Informasi sarana produksi pertanian yang mendukung kebutuhan pelaku usaha tani.',
            statistic: $saprodiTotal,
            statisticLabel: 'Jenis saprodi tercatat',
            columns: ['Nama Saprodi', 'Nama Poktan', 'Jumlah Diserahkan', 'Satuan', 'Tahun Diserahkan'],
            rows: $rows,
        );
    }

    public function crops(): View
    {
        $rows = Crop::query()
            ->select(['commodity', 'district', 'planted_area', 'harvested_area', 'production', 'period'])
            ->orderBy('commodity')
            ->limit(12)
            ->get()
            ->map(fn (Crop $crop): array => [
                $crop->commodity,
                $crop->district,
                number_format((float) $crop->planted_area, 0, ',', '.').' ha',
                number_format((float) $crop->harvested_area, 0, ',', '.').' ha',
                $crop->production,
                $crop->period,
            ])
            ->all();

        return $this->page(
            title: 'Tanaman Pangan',
            description: 'Ringkasan komoditas, luas tanam, dan hasil produksi tanaman pangan Kabupaten Kutai Barat.',
            statistic: Crop::distinct('commodity')->count('commodity'),
            statisticLabel: 'Komoditas tercatat',
            columns: ['Komoditas', 'Kecamatan', 'Luas Tanam', 'Luas Panen', 'Jumlah Produksi', 'Periode'],
            rows: $rows,
        );
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, array<int, string>>|LengthAwarePaginator  $rows
     */
    private function page(string $title, string $description, int $statistic, string $statisticLabel, array $columns, array|LengthAwarePaginator $rows): View
    {
        $chart = $this->chartForPage(request()->routeIs('public.alsintans'), request()->routeIs('public.poktans'), request()->routeIs('public.saprodis'), request()->routeIs('public.crops'));

        return view('public-information', compact('title', 'description', 'statistic', 'statisticLabel', 'columns', 'rows', 'chart') + ['yearCards' => $this->yearCards()]);
    }

    /**
     * @return array<int, array{label: string, title: string, value: int|string, description: string}>
     */
    private function yearCards(): array
    {
        $tahunAlsintan = Alsintan::query()
            ->whereNotNull('procurement_year')
            ->orderBy('procurement_year', 'desc')
            ->value('procurement_year');

        $tahunSaprodi = Saprodi::query()
            ->whereNotNull('distributed_year')
            ->orderBy('distributed_year', 'desc')
            ->value('distributed_year');

        $periodeTerbaru = Crop::query()
            ->whereNotNull('period')
            ->orderBy('period', 'desc')
            ->value('period');

        $tahunPoktan = Poktan::query()->latest()->first(['created_at'])?->created_at?->year;

        return [
            [
                'label' => 'Tahun Data',
                'title' => 'Poktan',
                'value' => $tahunPoktan ?? '—',
                'description' => 'Tahun data terbaru: '.($tahunPoktan ?? '—'),
            ],
            [
                'label' => 'Tahun Diserahkan',
                'title' => 'Alsintan',
                'value' => $tahunAlsintan ?? '—',
                'description' => 'Tahun penyerahan terbaru: '.($tahunAlsintan ?? '—'),
            ],
            [
                'label' => 'Tahun Diserahkan',
                'title' => 'Saprodi',
                'value' => $tahunSaprodi ?? '—',
                'description' => 'Tahun penyerahan terbaru: '.($tahunSaprodi ?? '—'),
            ],
            [
                'label' => 'Periode Data',
                'title' => 'Tanaman Pangan',
                'value' => $periodeTerbaru ?? '—',
                'description' => 'Periode data terbaru: '.($periodeTerbaru ?? '—'),
            ],
        ];
    }

    /**
     * Buat data chart pyramid sesuai halaman yang sedang dibuka.
     */
    private function chartForPage(bool $isAlsintans, bool $isPoktans, bool $isSaprodis, bool $isCrops): array
    {
        if ($isAlsintans) {
            return [
                ['label' => 'Jenis alsintan', 'value' => Alsintan::query()->distinct()->count('type')],
                ['label' => 'Kecamatan', 'value' => Alsintan::query()->distinct()->count('district')],
                ['label' => 'Tahun diserahkan', 'value' => Alsintan::query()->whereNotNull('procurement_year')->distinct()->count('procurement_year')],
            ];
        }

        if ($isPoktans) {
            return [
                ['label' => 'Kelompok tani', 'value' => Poktan::query()->where('status', 'Aktif')->distinct()->count('id')],
                ['label' => 'Kecamatan', 'value' => Poktan::query()->distinct()->count('district')],
                ['label' => 'Komoditas', 'value' => Poktan::query()->whereNotNull('commodity')->distinct('commodity')->count('commodity')],
            ];
        }

        if ($isSaprodis) {
            return [
                ['label' => 'Jenis saprodi', 'value' => Saprodi::query()->distinct()->count('name')],
                ['label' => 'Poktan penerima', 'value' => Saprodi::query()->whereNotNull('poktan_id')->distinct()->count('poktan_id')],
                ['label' => 'Satuan', 'value' => Saprodi::query()->whereNotNull('unit')->distinct('unit')->count('unit')],
            ];
        }

        if ($isCrops) {
            return [
                ['label' => 'Komoditas', 'value' => Crop::query()->whereNotNull('commodity')->distinct('commodity')->count('commodity')],
                ['label' => 'Kecamatan', 'value' => Crop::query()->distinct()->count('district')],
                ['label' => 'Panen', 'value' => Crop::query()->whereNotNull('period')->distinct('period')->count('period')],
            ];
        }

        return [
            ['label' => 'Jenis data', 'value' => 4],
            ['label' => 'Jumlah tabel', 'value' => 4],
            ['label' => 'Data publik', 'value' => Poktan::query()->where('status', 'Aktif')->count()],
        ];
    }
}
