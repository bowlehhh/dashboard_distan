<?php

namespace Tests\Feature;

use App\Models\Poktan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaprodiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_a_saprodi_without_a_poktan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('saprodis.store'), [
            'name' => 'Pupuk Organik',
            'unit' => 'Sak',
            'quantity_distributed' => 25,
            'distributed_year' => 2026,
        ]);

        $response->assertRedirect(route('saprodis.index'));
        $this->assertDatabaseHas('saprodis', [
            'name' => 'Pupuk Organik',
            'poktan_id' => null,
            'quantity_distributed' => 25,
        ]);
    }

    public function test_admin_can_save_a_saprodi_distribution(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $poktan = Poktan::query()->create([
            'name' => 'Tani Makmur',
            'chairperson' => 'Budi Hartono',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'commodity' => 'Padi',
            'member_count' => 20,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->post(route('saprodis.store'), [
            'name' => 'Selang Irigasi',
            'poktan_id' => $poktan->id,
            'unit' => 'Meter',
            'quantity_distributed' => 120,
            'distributed_year' => 2026,
        ]);

        $response->assertRedirect(route('saprodis.index'));
        $this->assertDatabaseHas('saprodis', [
            'name' => 'Selang Irigasi',
            'poktan_id' => $poktan->id,
            'quantity_distributed' => 120,
            'distributed_year' => 2026,
        ]);
    }
}
