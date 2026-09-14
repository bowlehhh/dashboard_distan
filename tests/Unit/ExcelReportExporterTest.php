<?php

namespace Tests\Unit;

use App\ExcelReportExporter;
use Illuminate\Http\Request;
use Tests\TestCase;
use ZipArchive;

class ExcelReportExporterTest extends TestCase
{
    public function test_download_contains_excel_compatible_workbook_with_all_rows(): void
    {
        $response = (new ExcelReportExporter)->download(
            'Rekap_Alsintan',
            'Rekap Alsintan',
            ['Jenis', 'Nomor Inventaris'],
            [
                ['Traktor', 'ALS-001'],
                ['Pompa Air', null],
            ],
        );
        $response->prepare(Request::create('/'));
        $path = $response->getFile()->getPathname();

        try {
            $zip = new ZipArchive;
            $this->assertTrue($zip->open($path));

            $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->close();

            $this->assertIsString($worksheet);
            $this->assertStringContainsString('Rekap Alsintan', $worksheet);
            $this->assertStringContainsString('Traktor', $worksheet);
            $this->assertStringContainsString('ALS-001', $worksheet);
            $this->assertStringContainsString('Pompa Air', $worksheet);
            $this->assertStringNotContainsString('tableParts', $worksheet);
            $this->assertSame('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
            $this->assertSame((string) filesize($path), $response->headers->get('Content-Length'));
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
