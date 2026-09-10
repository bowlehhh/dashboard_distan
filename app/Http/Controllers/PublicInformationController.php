<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

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
            chart: [['label' => 'Poktan aktif', 'value' => $poktanTotal, 'color' => '#0d7838']],
            recommendations: $this->makeRecommendations(collect(['Aktif' => $poktanTotal]), $poktanTotal, 'Status kelompok tani'),
        );
    }

    public function alsintans(): View
    {
        $conditionCounts = Alsintan::query()
            ->select('condition')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition');

        $alsintanTotal = Alsintan::query()->count();

        $rows = Alsintan::query()
            ->select(['type', 'brand_type', 'district', 'condition'])
            ->orderBy('type')
            ->paginate(4);

        $rows->getCollection()->transform(fn (Alsintan $alsintan): array => [
            $alsintan->type,
            $alsintan->brand_type,
            $alsintan->district,
            $alsintan->condition,
        ]);

        return $this->page(
            title: 'Data Alsintan',
            description: 'Ringkasan alat dan mesin pertanian untuk mendukung kegiatan budidaya di Kutai Barat.',
            statistic: $alsintanTotal,
            statisticLabel: 'Unit alsintan terdaftar',
            columns: ['Jenis Alsintan', 'Merk / Tipe', 'Kecamatan', 'Kondisi'],
            rows: $rows,
            chart: [
                ['label' => 'Baik', 'value' => (int) $conditionCounts->get('Baik', 0), 'color' => '#0d7838'],
                ['label' => 'Rusak Ringan', 'value' => (int) $conditionCounts->get('Rusak Ringan', 0), 'color' => '#a4c64b'],
                ['label' => 'Rusak Berat', 'value' => (int) $conditionCounts->get('Rusak Berat', 0), 'color' => '#f5c743'],
            ],
            recommendations: $this->makeRecommendations($conditionCounts, $alsintanTotal, 'Kondisi alsintan'),
        );
    }

    public function saprodis(): View
    {
        $saprodiCategoryCounts = Saprodi::query()
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->select('category')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('total', 'category');

        $saprodiTotal = Saprodi::query()->count();

        $rows = Saprodi::query()
            ->select(['name', 'category', 'stock', 'unit'])
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(fn (Saprodi $saprodi): array => [
                $saprodi->name,
                $saprodi->category,
                number_format((float) $saprodi->stock, 0, ',', '.'),
                $saprodi->unit,
            ])
            ->all();

        return $this->page(
            title: 'Data Saprodi',
            description: 'Informasi sarana produksi pertanian yang mendukung kebutuhan pelaku usaha tani.',
            statistic: $saprodiTotal,
            statisticLabel: 'Jenis saprodi tercatat',
            columns: ['Nama Saprodi', 'Kategori', 'Stok', 'Satuan'],
            rows: $rows,
            recommendations: $this->makeRecommendations($saprodiCategoryCounts, $saprodiTotal, 'Kategori saprodi'),
        );
    }

    public function crops(): View
    {
        $cropCommodityCounts = Crop::query()
            ->whereNotNull('commodity')
            ->where('commodity', '<>', '')
            ->select('commodity')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('commodity')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('total', 'commodity');

        $cropTotal = Crop::query()->count();

        $rows = Crop::query()
            ->select(['commodity', 'district', 'planted_area', 'production', 'unit'])
            ->orderBy('commodity')
            ->limit(12)
            ->get()
            ->map(fn (Crop $crop): array => [
                $crop->commodity,
                $crop->district,
                number_format((float) $crop->planted_area, 0, ',', '.').' ha',
                number_format((float) $crop->production, 0, ',', '.').' '.$crop->unit,
            ])
            ->all();

        return $this->page(
            title: 'Tanaman Pangan',
            description: 'Ringkasan komoditas, luas tanam, dan hasil produksi tanaman pangan Kabupaten Kutai Barat.',
            statistic: Crop::distinct('commodity')->count('commodity'),
            statisticLabel: 'Komoditas tercatat',
            columns: ['Komoditas', 'Kecamatan', 'Luas Tanam', 'Produksi'],
            rows: $rows,
            recommendations: $this->makeRecommendations($cropCommodityCounts, $cropTotal, 'Komoditas tanaman pangan'),
        );
    }

    public function reports(): View
    {
        $allReportCounts = collect([
            'Kelompok Tani' => Poktan::query()->count(),
            'Unit Alsintan' => Alsintan::query()->count(),
            'Jenis Saprodi' => Saprodi::query()->count(),
            'Data Tanaman Pangan' => Crop::query()->count(),
        ]);

        $reportTotal = $allReportCounts->sum();
        $reportCounts = $allReportCounts->sortDesc()->take(3);

        return $this->page(
            title: 'Laporan Pertanian',
            description: 'Ikhtisar data utama SIMANTAP untuk memantau perkembangan pertanian Kabupaten Kutai Barat.',
            statistic: $reportTotal,
            statisticLabel: 'Total data terintegrasi',
            columns: ['Indikator', 'Jumlah'],
            rows: [
                ['Kelompok Tani', number_format(Poktan::query()->count())],
                ['Unit Alsintan', number_format(Alsintan::query()->count())],
                ['Jenis Saprodi', number_format(Saprodi::query()->count())],
                ['Data Tanaman Pangan', number_format(Crop::query()->count())],
            ],
            recommendations: $this->makeRecommendations($reportCounts, $reportTotal, 'Jenis data teratas'),
        );
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, array<int, string>>|LengthAwarePaginator  $rows
     * @param  array<int, array{label: string, value: int, color: string}>|null  $chart
     * @param  array<int, array{tone: string, label: string, title: string, value: int, percentage: float, description: string}>|null  $recommendations
     */
    private function page(string $title, string $description, int $statistic, string $statisticLabel, array $columns, array|LengthAwarePaginator $rows, ?array $chart = null, ?array $recommendations = null): View
    {
        $chart ??= [['label' => $statisticLabel, 'value' => $statistic, 'color' => '#0d7838']];

        $recommendations ??= $statistic > 0
            ? [[
                'tone' => 'info',
                'label' => 'Ringkasan',
                'title' => $statisticLabel,
                'value' => $statistic,
                'percentage' => 100.0,
                'description' => 'Gunakan data ini sebagai bahan pemantauan dan koordinasi berikutnya.',
            ]]
            : [];

        return view('public-information', compact('title', 'description', 'statistic', 'statisticLabel', 'columns', 'rows', 'chart', 'recommendations'));
    }

    /**
     * @param  Collection<string, int|float>  $counts
     * @return array<int, array{tone: string, label: string, title: string, value: int, percentage: float, description: string}>
     */
    private function makeRecommendations(Collection $counts, int|float $total, string $groupLabel): array
    {
        if ($total <= 0) {
            return [];
        }

        return $counts->map(function (int|float $count, string $category) use ($groupLabel, $total): array {
            $percentage = round($count / $total * 100, 1);
            $tone = match (mb_strtolower($category)) {
                'rusak berat', 'nonaktif' => 'danger',
                'rusak ringan', 'perlu restok' => 'warning',
                'baik', 'tersedia', 'aktif' => 'success',
                default => 'info',
            };

            return [
                'tone' => $tone,
                'label' => $groupLabel,
                'title' => $category,
                'value' => (int) $count,
                'percentage' => $percentage,
                'description' => 'Mewakili '.$percentage.'% dari total data yang tersedia.',
            ];
        })->values()->all();
    }
}
