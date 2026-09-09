<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInformationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_lists_all_main_modules(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('Beranda')
            ->assertSee('Data Poktan')
            ->assertSee('Data Alsintan')
            ->assertSee('Data Saprodi')
            ->assertSee('Laporan')
            ->assertSee('Masuk')
            ->assertDontSee('Pengguna')
            ->assertDontSee('Pengaturan');
    }

    public function test_public_poktan_page_renders_without_authentication(): void
    {
        $response = $this->get(route('public.poktans'));

        $response->assertSee('Data Kelompok Tani')
            ->assertSee('public-directory-shell--empty');
    }

    public function test_public_alsintan_page_paginates_rows_without_authentication(): void
    {
        $this->seedAlsintans(9);

        $response = $this->get(route('public.alsintans'));

        $response->assertSee('Data Alsintan')
            ->assertSee('Rekomendasi singkat')
            ->assertSee('Rusak berat perlu ditangani')
            ->assertSee('Baik')
            ->assertSee('Rusak Ringan')
            ->assertSee('Rusak Berat')
            ->assertSee('9')
            ->assertSee('page=2')
            ->assertSee('page=3');

        $pageTwoResponse = $this->get(route('public.alsintans', ['page' => 2]));

        $pageTwoResponse->assertSee('Unit 05')
            ->assertSee('Unit 08')
            ->assertDontSee('Unit 01');
    }

    public function test_public_saprodi_page_renders_without_authentication(): void
    {
        $response = $this->get(route('public.saprodis'));

        $response->assertSee('Data Saprodi');
    }

    public function test_public_crop_page_renders_without_authentication(): void
    {
        $response = $this->get(route('public.crops'));

        $response->assertSee('Tanaman Pangan');
    }

    public function test_public_report_page_renders_without_authentication(): void
    {
        $response = $this->get(route('public.reports'));

        $response->assertSee('Laporan Pertanian')
            ->assertSee('public-directory-shell--empty');
    }

    private function seedAlsintans(int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            Alsintan::query()->create([
                'type' => sprintf('Unit %02d', $index),
                'brand_type' => sprintf('Brand %02d', $index),
                'inventory_number' => sprintf('INV-%02d', $index),
                'district' => sprintf('Kecamatan %02d', $index),
                'village' => sprintf('Desa %02d', $index),
                'procurement_year' => 2020,
                'condition' => 'Baik',
                'usage_status' => 'Digunakan',
            ]);
        }
    }
}
