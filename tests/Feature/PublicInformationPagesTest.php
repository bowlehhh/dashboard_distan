<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
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
            ->assertSee('Tanaman Pangan')
            ->assertDontSee('>Laporan<', false)
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

    public function test_public_poktan_page_only_includes_active_groups_in_the_table_and_chart(): void
    {
        Poktan::query()->create([
            'name' => 'Poktan Aktif',
            'chairperson' => 'Ketua Aktif',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'commodity' => 'Padi',
            'member_count' => 20,
            'status' => 'Aktif',
        ]);
        Poktan::query()->create([
            'name' => 'Poktan Nonaktif',
            'chairperson' => 'Ketua Nonaktif',
            'district' => 'Jempang',
            'village' => 'Tanjung Isuy',
            'commodity' => 'Jagung',
            'member_count' => 15,
            'status' => 'Nonaktif',
        ]);

        $response = $this->get(route('public.poktans'));

        $response->assertSee('Poktan Aktif')
            ->assertDontSee('Poktan Nonaktif')
            ->assertSee('Aktif')
            ->assertDontSee('Nonaktif')
            ->assertSee('Kecamatan');
    }

    public function test_public_poktan_page_paginates_active_groups(): void
    {
        $this->seedPoktans(9);

        $response = $this->get(route('public.poktans'));

        $response->assertSee('Poktan 01')
            ->assertSee('Poktan 08')
            ->assertDontSee('Poktan 09')
            ->assertSee('page=2');

        $pageTwoResponse = $this->get(route('public.poktans', ['page' => 2]));

        $pageTwoResponse->assertSee('Poktan 09')
            ->assertDontSee('Poktan 01');
    }

    public function test_public_alsintan_page_paginates_rows_without_authentication(): void
    {
        $this->seedAlsintans(9);

        $response = $this->get(route('public.alsintans'));

        $response->assertSee('Data Alsintan')
            ->assertSee('Tahun Diserahkan')
            ->assertDontSee('Rekomendasi singkat')
            ->assertDontSee('Kondisi')
            ->assertSee('Ringkasan Tahun Data')
            ->assertSee('Poktan')
            ->assertSee('Saprodi')
            ->assertSee('Tanaman Pangan')
            ->assertSee('9')
            ->assertSee('page=2')
            ->assertDontSee('page=3');

        $pageTwoResponse = $this->get(route('public.alsintans', ['page' => 2]));

        $pageTwoResponse->assertSee('Unit 09')
            ->assertDontSee('Unit 01');
    }

    public function test_public_saprodi_page_renders_without_authentication(): void
    {
        $response = $this->get(route('public.saprodis'));

        $response->assertSee('Data Saprodi');
    }

    public function test_public_saprodi_page_paginates_rows(): void
    {
        $this->seedSaprodis(9);

        $response = $this->get(route('public.saprodis'));

        $response->assertSee('Saprodi 01')
            ->assertSee('Saprodi 08')
            ->assertDontSee('Saprodi 09')
            ->assertSee('page=2');

        $pageTwoResponse = $this->get(route('public.saprodis', ['page' => 2]));

        $pageTwoResponse->assertSee('Saprodi 09')
            ->assertDontSee('Saprodi 01');
    }

    public function test_public_crop_page_renders_without_authentication(): void
    {
        $response = $this->get(route('public.crops'));

        $response->assertSee('Tanaman Pangan');
    }

    public function test_public_crop_page_paginates_rows(): void
    {
        $this->seedCrops(9);

        $response = $this->get(route('public.crops'));

        $response->assertSee('Komoditas 01')
            ->assertSee('Komoditas 08')
            ->assertDontSee('Komoditas 09')
            ->assertSee('page=2');

        $pageTwoResponse = $this->get(route('public.crops', ['page' => 2]));

        $pageTwoResponse->assertSee('Komoditas 09')
            ->assertDontSee('Komoditas 01');
    }

    public function test_public_report_url_redirects_to_the_crop_page(): void
    {
        $response = $this->get(route('public.reports'));

        $response->assertRedirect(route('public.crops'));
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
                'usage_status' => 'Digunakan',
            ]);
        }
    }

    private function seedPoktans(int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            Poktan::query()->create([
                'name' => sprintf('Poktan %02d', $index),
                'chairperson' => sprintf('Ketua %02d', $index),
                'district' => 'Melak',
                'village' => 'Melak Ulu',
                'commodity' => 'Padi',
                'member_count' => 20,
                'status' => 'Aktif',
            ]);
        }
    }

    private function seedSaprodis(int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            Saprodi::query()->create([
                'name' => sprintf('Saprodi %02d', $index),
                'unit' => 'Sak',
                'quantity_distributed' => 10,
                'distributed_year' => 2026,
            ]);
        }
    }

    private function seedCrops(int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            Crop::query()->create([
                'commodity' => sprintf('Komoditas %02d', $index),
                'district' => 'Melak',
                'planted_area' => 10,
                'harvested_area' => 8,
                'production' => 12,
                'period' => '2026',
            ]);
        }
    }
}
