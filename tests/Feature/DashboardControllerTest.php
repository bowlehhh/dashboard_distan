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
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Alsintan']));

        $response->assertOk()
            ->assertSee('Merek / Tipe')
            ->assertSee('No. Inventaris')
            ->assertSee('Tahun')
            ->assertSee('Penggunaan')
            ->assertSee('Yanmar TF 85')
            ->assertSee('INV-001')
            ->assertSee('2026')
            ->assertSee('Digunakan')
            ->assertSee('Baik')
            ->assertDontSee('Detail Informasi')
            ->assertDontSee('>Data Input<', false);
    }

    public function test_dashboard_renders_saprodi_fields_as_separate_columns(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Saprodi::query()->create([
            'name' => 'Pupuk Urea',
            'category' => 'Pupuk',
            'unit' => 'Kg',
            'stock' => 1250,
            'minimum_stock' => 500,
            'photo_path' => 'saprodi/pupuk-urea.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Saprodi']));

        $response->assertOk()
            ->assertSee('Nama Saprodi')
            ->assertSee('Pupuk Urea')
            ->assertSee('Kategori')
            ->assertSee('Pupuk')
            ->assertSee('1.250,00')
            ->assertSee('Kg')
            ->assertSee('500,00')
            ->assertSee('Tersedia')
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
        $poktan = Poktan::query()->create([
            'name' => 'Suka Maju',
            'chairperson' => 'Joko Santoso',
            'district' => 'Barong Tongkok',
            'village' => 'Geleo Baru',
            'commodity' => 'Padi',
            'member_count' => 28,
            'status' => 'Aktif',
        ]);
        Crop::query()->create([
            'commodity' => 'Padi',
            'poktan_id' => $poktan->id,
            'district' => 'Barong Tongkok',
            'planted_area' => 10,
            'harvested_area' => 8,
            'production' => 27100,
            'unit' => 'Ton',
            'period' => '2026',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Tanaman Pangan']));

        $response->assertOk()
            ->assertSee('Komoditas')
            ->assertSee('Luas Tanam')
            ->assertSee('Luas Panen')
            ->assertSee('Produksi')
            ->assertSee('Satuan')
            ->assertSee('Periode')
            ->assertSee('10 Ha')
            ->assertSee('8 Ha')
            ->assertSee('27.100')
            ->assertSee('Ton')
            ->assertSee('2026')
            ->assertSee('Suka Maju')
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

    public function test_dashboard_hides_action_links_for_ppl_users(): void
    {
        $user = User::factory()->create(['role' => 'ppl']);
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

    public function test_dashboard_hides_empty_minimum_stock_column_value(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Saprodi::query()->create([
            'name' => 'Benih Padi',
            'category' => 'Benih',
            'unit' => 'Kg',
            'stock' => 300,
            'minimum_stock' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['type' => 'Saprodi']));

        $response->assertOk()
            ->assertSee('Benih Padi')
            ->assertSee('Benih')
            ->assertSee('300,00')
            ->assertDontSee('500,00');
    }
}
