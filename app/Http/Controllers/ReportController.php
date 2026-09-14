<?php

namespace App\Http\Controllers;

use App\ExcelReportExporter;
use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    public function download(string $report, ExcelReportExporter $excelReportExporter): BinaryFileResponse
    {
        $data = match ($report) {
            'alsintan' => [
                'filename' => 'Rekap_Alsintan',
                'title' => 'Rekap Alsintan',
                'headers' => ['Jenis Alsintan', 'Merk / Tipe', 'Nomor Inventaris', 'Poktan', 'Kecamatan', 'Kampung / Desa', 'Tahun Diserahkan', 'Kondisi', 'Status Penggunaan'],
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
                'title' => 'Rekap Saprodi',
                'headers' => ['Nama Saprodi', 'Nama Poktan', 'Satuan', 'Jumlah Diserahkan', 'Tahun Diserahkan', 'Keterangan'],
                'rows' => Saprodi::with('poktan')->orderBy('name')->get()->map(fn (Saprodi $item): array => [
                    $item->name,
                    $item->poktan?->name ?? '',
                    $item->unit,
                    number_format((float) $item->quantity_distributed, 0, ',', '.'),
                    $item->distributed_year ?? '',
                    $item->notes ?? '',
                ]),
            ],
            'tanaman-pangan' => [
                'filename' => 'Rekap_Tanaman_Pangan',
                'title' => 'Rekap Tanaman Pangan',
                'headers' => ['Komoditas', 'Kecamatan', 'Luas Tanam (Ha)', 'Luas Panen (Ha)', 'Produksi', 'Periode'],
                'rows' => Crop::orderBy('commodity')->get()->map(fn (Crop $item): array => [
                    $item->commodity,
                    $item->district,
                    number_format((float) $item->planted_area, 2, ',', '.'),
                    number_format((float) $item->harvested_area, 2, ',', '.'),
                    $item->production,
                    $item->period,
                ]),
            ],
            'poktan' => [
                'filename' => 'Data_Kelompok_Tani',
                'title' => 'Data Kelompok Tani',
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

        return $excelReportExporter->download($data['filename'], $data['title'], $data['headers'], $data['rows']);
    }
}
