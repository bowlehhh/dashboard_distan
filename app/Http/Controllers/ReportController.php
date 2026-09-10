<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', ['reports' => [
            ['label' => 'Rekap Alsintan', 'count' => Alsintan::count(), 'route' => 'alsintans.index', 'export' => 'alsintan'],
            ['label' => 'Rekap Saprodi', 'count' => Saprodi::count(), 'route' => 'saprodis.index', 'export' => 'saprodi'],
            ['label' => 'Tanaman Pangan', 'count' => Crop::count(), 'route' => 'crops.index', 'export' => 'tanaman-pangan'],
            ['label' => 'Data Poktan', 'count' => Poktan::count(), 'route' => 'poktans.index', 'export' => 'poktan'],
        ]]);
    }

    public function download(string $report): StreamedResponse
    {
        [$headers, $rows] = match ($report) {
            'alsintan' => [['Jenis Alsintan', 'Merk / Tipe', 'Nomor Inventaris', 'Poktan', 'Kecamatan', 'Kampung / Desa', 'Tahun Pengadaan', 'Kondisi', 'Status Penggunaan'], Alsintan::with('poktan')->orderBy('type')->get()->map(fn (Alsintan $item): array => [$item->type, $item->brand_type, $item->inventory_number, $item->poktan?->name ?? '', $item->district, $item->village, $item->procurement_year, $item->condition, $item->usage_status])],
            'saprodi' => [['Nama Saprodi', 'Kategori', 'Satuan', 'Stok', 'Batas Minimum', 'Keterangan'], Saprodi::orderBy('name')->get()->map(fn (Saprodi $item): array => [$item->name, $item->category, $item->unit, $item->stock, $item->minimum_stock, $item->notes])],
            'tanaman-pangan' => [['Komoditas', 'Poktan', 'Kecamatan', 'Luas Tanam', 'Luas Panen', 'Produksi', 'Satuan', 'Periode'], Crop::with('poktan')->orderBy('commodity')->get()->map(fn (Crop $item): array => [$item->commodity, $item->poktan?->name ?? '', $item->district, $item->planted_area, $item->harvested_area, $item->production, $item->unit, $item->period])],
            'poktan' => [['Nama Poktan', 'Ketua', 'Kecamatan', 'Kampung / Desa', 'Komoditas', 'Jumlah Anggota', 'Kontak', 'Status'], Poktan::orderBy('name')->get()->map(fn (Poktan $item): array => [$item->name, $item->chairperson, $item->district, $item->village, $item->commodity, $item->member_count, $item->phone, $item->status])],
        };

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, 'rekap-'.$report.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
