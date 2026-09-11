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
        $data = match ($report) {
            'alsintan' => [
                'filename' => 'Rekap_Alsintan',
                'headers' => ['Jenis Alsintan', 'Merk / Tipe', 'Nomor Inventaris', 'Poktan', 'Kecamatan', 'Kampung / Desa', 'Tahun Pengadaan', 'Kondisi', 'Status Penggunaan'],
                'rows' => Alsintan::with('poktan')->orderBy('type')->get()->map(fn (Alsintan $item): array => [
                    $item->type,
                    $item->brand_type,
                    $item->inventory_number,
                    $item->poktan?->name ?? '',
                    $item->district,
                    $item->village,
                    $item->procurement_year,
                    $item->condition,
                    $item->usage_status,
                ]),
            ],
            'saprodi' => [
                'filename' => 'Rekap_Saprodi',
                'headers' => ['Nama Saprodi', 'Kategori', 'Satuan', 'Stok', 'Batas Minimum', 'Keterangan'],
                'rows' => Saprodi::orderBy('name')->get()->map(fn (Saprodi $item): array => [
                    $item->name,
                    $item->category,
                    $item->unit,
                    number_format((float) $item->stock, 0, ',', '.'),
                    $item->minimum_stock > 0 ? number_format((float) $item->minimum_stock, 0, ',', '.') : 0,
                    $item->notes ?? '',
                ]),
            ],
            'tanaman-pangan' => [
                'filename' => 'Rekap_Tanaman_Pangan',
                'headers' => ['Komoditas', 'Poktan', 'Kecamatan', 'Luas Tanam (Ha)', 'Luas Panen (Ha)', 'Produksi', 'Satuan', 'Periode'],
                'rows' => Crop::with('poktan')->orderBy('commodity')->get()->map(fn (Crop $item): array => [
                    $item->commodity,
                    $item->poktan?->name ?? '',
                    $item->district,
                    number_format((float) $item->planted_area, 2, ',', '.'),
                    number_format((float) $item->harvested_area, 2, ',', '.'),
                    number_format((float) $item->production, 0, ',', '.'),
                    $item->unit,
                    $item->period,
                ]),
            ],
            'poktan' => [
                'filename' => 'Data_Kelompok_Tani',
                'headers' => ['Nama Poktan', 'Ketua', 'Kecamatan', 'Kampung / Desa', 'Komoditas', 'Jumlah Anggota', 'Kontak', 'Status'],
                'rows' => Poktan::orderBy('name')->get()->map(fn (Poktan $item): array => [
                    $item->name,
                    $item->chairperson,
                    $item->district,
                    $item->village,
                    $item->commodity,
                    $item->member_count,
                    $item->phone ?? '',
                    $item->status,
                ]),
            ],
        };

        // Create CSV content with proper formatting
        $output = fopen('php://temp', 'r+');

        // Write BOM for UTF-8 support in Excel
        fwrite($output, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($output, $data['headers']);

        // Write data rows
        foreach ($data['rows'] as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        // Return as CSV that Excel can open natively
        return response()->streamDownload(function () use ($csvContent): void {
            echo $csvContent;
        }, $data['filename'].'-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename*="utf-8\'\''.$data['filename'].'-'.now()->format('Ymd-His').'.csv',
        ]);
    }
}
