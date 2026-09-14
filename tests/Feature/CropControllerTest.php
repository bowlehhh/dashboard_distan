<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CropControllerTest extends TestCase
{
    public function test_admin_can_save_a_production_value_with_its_unit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('crops.store'), [
            'commodity' => 'Padi',
            'district' => 'Melak',
            'planted_area' => 12.5,
            'harvested_area' => 10,
            'production' => '1500 Kg',
            'period' => '2026',
        ]);

        $response->assertRedirect(route('crops.index'));
        $this->assertDatabaseHas('crops', [
            'commodity' => 'Padi',
            'production' => '1500 Kg',
        ]);
    }
}
