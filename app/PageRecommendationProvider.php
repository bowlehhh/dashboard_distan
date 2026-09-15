<?php

namespace App;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Http\Request;

class PageRecommendationProvider
{
    /**
     * Build concise, role-gated information cards for an index page.
     *
     * @return array<int, array{title: string, value: string, description: string}>
     */
    public function forIndexPage(Request $request, ?User $user): array
    {
        if (! $user?->hasRole('admin', 'pimpinan')) {
            return [];
        }

        return match ($request->route()?->getName()) {
            'alsintans.index' => $this->alsintanCards(),
            'saprodis.index' => $this->saprodiCards(),
            'crops.index' => $this->cropCards(),
            'poktans.index' => $this->poktanCards(),
            default => [],
        };
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function alsintanCards(): array
    {
        $counts = Alsintan::query()
            ->select('condition')
            ->selectRaw('count(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition');

        return [
            ['title' => 'Kondisi Baik', 'value' => (string) ($counts['Baik'] ?? 0), 'description' => 'Unit yang siap digunakan untuk kegiatan pertanian.'],
            ['title' => 'Rusak Ringan', 'value' => (string) ($counts['Rusak Ringan'] ?? 0), 'description' => 'Unit yang perlu perawatan atau perbaikan ringan.'],
            ['title' => 'Rusak Berat', 'value' => (string) ($counts['Rusak Berat'] ?? 0), 'description' => 'Unit yang perlu ditindaklanjuti dengan perbaikan menyeluruh.'],
        ];
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function saprodiCards(): array
    {
        $metrics = Saprodi::query()
            ->selectRaw('max(distributed_year) as latest_year, count(distinct poktan_id) as recipient_count, count(*) as total')
            ->firstOrFail();
        $latestYear = $metrics->latest_year;

        return [
            ['title' => 'Tahun Penyerahan Terbaru', 'value' => $latestYear ? (string) $latestYear : '—', 'description' => $latestYear ? 'Data penyerahan saprodi paling baru tercatat pada tahun '.$latestYear.'.' : 'Belum ada data tahun penyerahan saprodi.'],
            ['title' => 'Poktan Penerima', 'value' => (string) $metrics->recipient_count, 'description' => 'Jumlah kelompok tani yang menerima saprodi.'],
            ['title' => 'Total Jenis Saprodi', 'value' => (string) $metrics->total, 'description' => 'Jumlah data saprodi yang telah diserahkan.'],
        ];
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function cropCards(): array
    {
        $metrics = Crop::query()
            ->selectRaw('max(period) as latest_period, sum(planted_area) as planted_area, sum(harvested_area) as harvested_area')
            ->firstOrFail();
        $latestPeriod = $metrics->latest_period;

        return [
            ['title' => 'Periode Terbaru', 'value' => $latestPeriod ? (string) $latestPeriod : '—', 'description' => $latestPeriod ? 'Realisasi tanam dan panen terakhir pada periode '.$latestPeriod.'.' : 'Belum ada periode tanaman pangan yang tercatat.'],
            ['title' => 'Total Luas Tanam', 'value' => $this->formatArea((float) $metrics->planted_area), 'description' => 'Akumulasi luas tanam dari seluruh data komoditas.'],
            ['title' => 'Total Luas Panen', 'value' => $this->formatArea((float) $metrics->harvested_area), 'description' => 'Akumulasi luas panen dari seluruh data komoditas.'],
        ];
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function poktanCards(): array
    {
        $metrics = Poktan::query()
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as active_count, sum(case when status = ? then 1 else 0 end) as inactive_count, count(distinct district) as district_count', ['Aktif', 'Nonaktif'])
            ->firstOrFail();

        return [
            ['title' => 'Poktan Aktif', 'value' => (string) ($metrics->active_count ?? 0), 'description' => 'Kelompok tani yang berstatus aktif.'],
            ['title' => 'Poktan Nonaktif', 'value' => (string) ($metrics->inactive_count ?? 0), 'description' => 'Kelompok tani yang perlu pemutakhiran status.'],
            ['title' => 'Kecamatan Terjangkau', 'value' => (string) $metrics->district_count, 'description' => 'Jumlah kecamatan yang memiliki data kelompok tani.'],
        ];
    }

    private function formatArea(float $area): string
    {
        return rtrim(rtrim(number_format($area, 2, ',', '.'), '0'), ',').' Ha';
    }
}
