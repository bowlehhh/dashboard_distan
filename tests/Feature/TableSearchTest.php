<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_poktan_table_searches_across_the_displayed_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createPoktan('Tani Makmur', ['village' => 'Sungai Kedang']);
        $this->createPoktan('Subur Jaya', ['village' => 'Linggang']);

        $response = $this->actingAs($admin)->get(route('poktans.index', ['search' => 'Sungai Kedang']));

        $response->assertSee('Tani Makmur')
            ->assertDontSee('Subur Jaya')
            ->assertSee('type="search"', false)
            ->assertSee('⌕ Cari');
    }

    public function test_alsintan_table_searches_by_related_poktan_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $matchingPoktan = $this->createPoktan('Harapan Baru');
        $otherPoktan = $this->createPoktan('Karya Tani');
        $this->createAlsintan('Traktor Sungai', $matchingPoktan);
        $this->createAlsintan('Pompa Bukit', $otherPoktan);

        $response = $this->actingAs($admin)->get(route('alsintans.index', ['search' => 'Harapan Baru']));

        $response->assertSee('Traktor Sungai')
            ->assertSee('Harapan Baru')
            ->assertDontSee('Pompa Bukit');
    }

    public function test_saprodi_table_searches_by_related_poktan_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $matchingPoktan = $this->createPoktan('Bina Usaha');
        $otherPoktan = $this->createPoktan('Maju Bersama');
        $this->createSaprodi('Pupuk Organik', $matchingPoktan);
        $this->createSaprodi('Benih Jagung', $otherPoktan);

        $response = $this->actingAs($admin)->get(route('saprodis.index', ['search' => 'Bina Usaha']));

        $response->assertSee('Pupuk Organik')
            ->assertSee('Bina Usaha')
            ->assertDontSee('Benih Jagung');
    }

    public function test_crop_table_searches_by_production_information(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createCrop('Padi Sawah', '125 Ton Premium');
        $this->createCrop('Jagung Manis', '80 Ton Pipilan');

        $response = $this->actingAs($admin)->get(route('crops.index', ['search' => '125 Ton']));

        $response->assertSee('Padi Sawah')
            ->assertDontSee('Jagung Manis');
    }

    public function test_user_table_searches_by_name_email_role_or_work_unit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'name' => 'Penyuluh Melak',
            'email' => 'melak@example.test',
            'role' => 'penyuluh',
            'unit_kerja' => 'Bidang Tanaman Pangan',
        ]);
        User::factory()->create([
            'name' => 'Penyuluh Barong',
            'email' => 'barong@example.test',
            'role' => 'penyuluh',
            'unit_kerja' => 'Bidang Alsintan',
        ]);

        $response = $this->actingAs($admin)->get(route('users.index', ['search' => 'Tanaman Pangan']));

        $response->assertSee('Penyuluh Melak')
            ->assertDontSee('Penyuluh Barong');
    }

    /** @param array<string, mixed> $attributes */
    private function createPoktan(string $name, array $attributes = []): Poktan
    {
        return Poktan::query()->create(array_merge([
            'name' => $name,
            'chairperson' => 'Ketua '.$name,
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'commodity' => 'Padi',
            'member_count' => 20,
            'status' => 'Aktif',
        ], $attributes));
    }

    private function createAlsintan(string $type, Poktan $poktan): Alsintan
    {
        return Alsintan::query()->create([
            'type' => $type,
            'brand_type' => 'Quick G1000',
            'poktan_id' => $poktan->id,
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
        ]);
    }

    private function createSaprodi(string $name, Poktan $poktan): Saprodi
    {
        return Saprodi::query()->create([
            'name' => $name,
            'poktan_id' => $poktan->id,
            'unit' => 'Sak',
            'quantity_distributed' => 25,
            'distributed_year' => 2026,
        ]);
    }

    private function createCrop(string $commodity, string $production): Crop
    {
        return Crop::query()->create([
            'commodity' => $commodity,
            'district' => 'Melak',
            'planted_area' => 12,
            'harvested_area' => 10,
            'production' => $production,
            'period' => '2026',
        ]);
    }
}
