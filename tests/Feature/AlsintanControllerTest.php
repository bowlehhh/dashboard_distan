<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AlsintanControllerTest extends TestCase
{
    public function test_admin_can_save_an_alsintan_google_maps_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('alsintans.store'), [
            'type' => 'Traktor Roda 2',
            'brand_type' => 'Yanmar TF 85',
            'inventory_number' => 'ALS-001',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
            'google_maps_url' => 'https://maps.app.goo.gl/ExampleLocation',
        ]);

        $response->assertRedirect(route('alsintans.index'));
        $this->assertDatabaseHas('alsintans', [
            'inventory_number' => 'ALS-001',
            'google_maps_url' => 'https://maps.app.goo.gl/ExampleLocation',
        ]);
    }

    public function test_alsintan_rejects_a_non_google_maps_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->from(route('alsintans.create'))->actingAs($admin)->post(route('alsintans.store'), [
            'type' => 'Traktor Roda 2',
            'brand_type' => 'Yanmar TF 85',
            'inventory_number' => 'ALS-001',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
            'google_maps_url' => 'https://example.com/location',
        ]);

        $response->assertRedirect(route('alsintans.create'))
            ->assertSessionHasErrors('google_maps_url');
    }
}
