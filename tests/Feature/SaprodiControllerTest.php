<?php

namespace Tests\Feature;

use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaprodiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_a_custom_saprodi_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('saprodis.store'), [
            'name' => 'Selang Irigasi',
            'category' => 'Lainnya',
            'custom_category' => 'Perlengkapan Irigasi',
            'unit' => 'Meter',
            'stock' => 120,
            'minimum_stock' => 30,
        ]);

        $response->assertRedirect(route('saprodis.index'));
        $this->assertSame('Perlengkapan Irigasi', Saprodi::query()->sole()->category);
    }
}
