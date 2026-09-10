<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_labels_alsintan_brand_type_as_input_data(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Alsintan::query()->create([
            'type' => 'Traktor Roda 2',
            'brand_type' => 'Yamaha',
            'inventory_number' => 'ALS-YMH-001',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Data Input')
            ->assertSee('Merek / Tipe')
            ->assertSee('Yamaha')
            ->assertDontSee('>Detail<', false);
    }

    public function test_dashboard_maps_saprodi_fields_to_the_main_summary_table(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Saprodi::query()->create([
            'name' => 'Pupuk Organik',
            'category' => 'Pupuk',
            'unit' => 'Kg',
            'stock' => 1250,
            'minimum_stock' => 500,
            'photo_path' => 'saprodi/pupuk-organik.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Pupuk Organik')
            ->assertSee('Kategori')
            ->assertSee('Pupuk')
            ->assertSee('1.250,00 Kg')
            ->assertSee('Tersedia')
            ->assertSee('storage/saprodi/pupuk-organik.jpg');
    }
}
