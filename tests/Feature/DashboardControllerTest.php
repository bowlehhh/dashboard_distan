<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_alsintan_fields_as_table_headers(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Alsintan::query()->create([
            'type' => 'Traktor Roda 2',
            'brand_type' => 'Yanmar TF 85',
            'inventory_number' => 'INV-001',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'usage_status' => 'Digunakan',
            'google_maps_url' => 'https://maps.app.goo.gl/ExampleLocation',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Alsintan']));

        $response->assertOk()
            ->assertSee('Merek / Tipe')
            ->assertSee('No. Inventaris')
            ->assertSee('Tahun')
            ->assertSee('Penggunaan')
            ->assertSee('Google Maps')
            ->assertSee('Yanmar TF 85')
            ->assertSee('INV-001')
            ->assertSee('2026')
            ->assertSee('Digunakan')
            ->assertSee('href="https://maps.app.goo.gl/ExampleLocation"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('Buka Google Maps')
            ->assertSee('Tahun Diserahkan')
            ->assertDontSee('Detail Informasi')
            ->assertDontSee('>Data Input<', false);
    }

    public function test_dashboard_renders_saprodi_fields_as_separate_columns(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $poktan = Poktan::query()->create([
            'name' => 'Tani Makmur',
            'chairperson' => 'Budi Hartono',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'commodity' => 'Padi',
            'member_count' => 20,
            'status' => 'Aktif',
        ]);
        Saprodi::query()->create([
            'name' => 'Pupuk Urea',
            'poktan_id' => $poktan->id,
            'unit' => 'Kg',
            'quantity_distributed' => 1250,
            'distributed_year' => 2026,
            'photo_path' => 'saprodi/pupuk-urea.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Saprodi']));

        $response->assertOk()
            ->assertSee('Nama Saprodi')
            ->assertSee('Pupuk Urea')
            ->assertSee('Nama Poktan')
            ->assertSee('Tani Makmur')
            ->assertSee('Jumlah Diserahkan')
            ->assertSee('1.250,00')
            ->assertSee('Kg')
            ->assertSee('Tahun Diserahkan')
            ->assertSee('2026')
            ->assertSee('storage/saprodi/pupuk-urea.jpg')
            ->assertDontSee('Detail Informasi');
    }

    public function test_dashboard_renders_poktan_fields_as_separate_columns(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Poktan::query()->create([
            'name' => 'Suka Maju',
            'chairperson' => 'Joko Santoso',
            'district' => 'Barong Tongkok',
            'village' => 'Geleo Baru',
            'commodity' => 'Padi',
            'member_count' => 28,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Poktan']));

        $response->assertOk()
            ->assertSee('Nama Poktan')
            ->assertSee('Suka Maju')
            ->assertSee('Ketua')
            ->assertSee('Joko Santoso')
            ->assertSee('Anggota')
            ->assertSee('28 orang')
            ->assertSee('Aktif')
            ->assertDontSee('Detail Informasi');
    }

    public function test_dashboard_renders_crop_fields_as_separate_columns(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Crop::query()->create([
            'commodity' => 'Padi',
            'district' => 'Barong Tongkok',
            'planted_area' => 10,
            'harvested_area' => 8,
            'production' => '27100 Ton',
            'period' => '2026',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Tanaman Pangan']));

        $response->assertOk()
            ->assertSee('Komoditas')
            ->assertSee('Luas Tanam')
            ->assertSee('Luas Panen')
            ->assertSee('Produksi')
            ->assertSee('Periode')
            ->assertSee('10 Ha')
            ->assertSee('8 Ha')
            ->assertSee('27100 Ton')
            ->assertSee('2026')
            ->assertDontSee('Kelompok Tani')
            ->assertDontSee('Detail Informasi');
    }

    public function test_dashboard_without_type_filter_uses_common_columns_only(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Poktan::query()->create([
            'name' => 'Suka Maju',
            'chairperson' => 'Joko Santoso',
            'district' => 'Barong Tongkok',
            'village' => 'Geleo Baru',
            'commodity' => 'Padi',
            'member_count' => 28,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Jenis Data')
            ->assertSee('Nama / Item')
            ->assertSee('Poktan')
            ->assertSee('Suka Maju')
            ->assertSee('Barong Tongkok')
            ->assertSee('Geleo Baru')
            ->assertSee('Padi')
            ->assertSee('Aktif')
            ->assertDontSee('Detail Informasi')
            ->assertDontSee('Ketua')
            ->assertDontSee('Anggota');
    }

    public function test_dashboard_does_not_render_a_google_maps_link_for_data_without_coordinates(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Poktan::query()->create([
            'name' => 'Suka Maju',
            'chairperson' => 'Joko Santoso',
            'district' => 'Barong Tongkok',
            'village' => 'Geleo Baru',
            'commodity' => 'Padi',
            'member_count' => 28,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Suka Maju')
            ->assertDontSee('Buka Google Maps');
    }

    public function test_dashboard_hides_action_links_for_penyuluh_users(): void
    {
        $user = User::factory()->create(['role' => 'penyuluh']);
        Poktan::query()->create([
            'name' => 'Tani Makmur',
            'chairperson' => 'Budi Hartono',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'commodity' => 'Jagung',
            'member_count' => 12,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Poktan']));

        $response->assertOk()
            ->assertSee('Ketua')
            ->assertSee('Budi Hartono')
            ->assertSee('12 orang')
            ->assertDontSee('poktans/1/edit');
    }

    public function test_dashboard_renders_saprodi_distribution_year(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $poktan = Poktan::query()->create([
            'name' => 'Suka Maju',
            'chairperson' => 'Joko Santoso',
            'district' => 'Barong Tongkok',
            'village' => 'Geleo Baru',
            'commodity' => 'Padi',
            'member_count' => 28,
            'status' => 'Aktif',
        ]);
        Saprodi::query()->create([
            'name' => 'Benih Padi',
            'poktan_id' => $poktan->id,
            'unit' => 'Kg',
            'quantity_distributed' => 300,
            'distributed_year' => 2025,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Saprodi']));

        $response->assertOk()
            ->assertSee('Benih Padi')
            ->assertSee('Suka Maju')
            ->assertSee('300,00')
            ->assertSee('2025');
    }
}
