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
        return [
            ['title' => 'Kondisi Baik', 'value' => (string) Alsintan::query()->where('condition', 'Baik')->count(), 'description' => 'Unit yang siap digunakan untuk kegiatan pertanian.'],
            ['title' => 'Rusak Ringan', 'value' => (string) Alsintan::query()->where('condition', 'Rusak Ringan')->count(), 'description' => 'Unit yang perlu perawatan atau perbaikan ringan.'],
            ['title' => 'Rusak Berat', 'value' => (string) Alsintan::query()->where('condition', 'Rusak Berat')->count(), 'description' => 'Unit yang perlu ditindaklanjuti dengan perbaikan menyeluruh.'],
        ];
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function saprodiCards(): array
    {
        $latestYear = Saprodi::query()->max('distributed_year');

        return [
            ['title' => 'Tahun Penyerahan Terbaru', 'value' => $latestYear ? (string) $latestYear : '—', 'description' => $latestYear ? 'Data penyerahan saprodi paling baru tercatat pada tahun '.$latestYear.'.' : 'Belum ada data tahun penyerahan saprodi.'],
            ['title' => 'Poktan Penerima', 'value' => (string) Saprodi::query()->whereNotNull('poktan_id')->distinct('poktan_id')->count('poktan_id'), 'description' => 'Jumlah kelompok tani yang menerima saprodi.'],
            ['title' => 'Total Jenis Saprodi', 'value' => (string) Saprodi::query()->count(), 'description' => 'Jumlah data saprodi yang telah diserahkan.'],
        ];
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function cropCards(): array
    {
        $latestPeriod = Crop::query()->max('period');

        return [
            ['title' => 'Periode Terbaru', 'value' => $latestPeriod ? (string) $latestPeriod : '—', 'description' => $latestPeriod ? 'Realisasi tanam dan panen terakhir pada periode '.$latestPeriod.'.' : 'Belum ada periode tanaman pangan yang tercatat.'],
            ['title' => 'Total Luas Tanam', 'value' => $this->formatArea((float) Crop::query()->sum('planted_area')), 'description' => 'Akumulasi luas tanam dari seluruh data komoditas.'],
            ['title' => 'Total Luas Panen', 'value' => $this->formatArea((float) Crop::query()->sum('harvested_area')), 'description' => 'Akumulasi luas panen dari seluruh data komoditas.'],
        ];
    }

    /** @return array<int, array{title: string, value: string, description: string}> */
    private function poktanCards(): array
    {
        return [
            ['title' => 'Poktan Aktif', 'value' => (string) Poktan::query()->where('status', 'Aktif')->count(), 'description' => 'Kelompok tani yang berstatus aktif.'],
            ['title' => 'Poktan Nonaktif', 'value' => (string) Poktan::query()->where('status', 'Nonaktif')->count(), 'description' => 'Kelompok tani yang perlu pemutakhiran status.'],
            ['title' => 'Kecamatan Terjangkau', 'value' => (string) Poktan::query()->distinct('district')->count('district'), 'description' => 'Jumlah kecamatan yang memiliki data kelompok tani.'],
        ];
    }

    private function formatArea(float $area): string
    {
        return rtrim(rtrim(number_format($area, 2, ',', '.'), '0'), ',').' Ha';
    }
}
