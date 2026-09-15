<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlsintanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_an_alsintan_without_an_inventory_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('alsintans.store'), [
            'type' => 'Cultivator',
            'brand_type' => 'Honda F300',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
        ]);

        $response->assertRedirect(route('alsintans.index'));
        $this->assertDatabaseHas('alsintans', [
            'type' => 'Cultivator',
            'inventory_number' => null,
        ]);
    }

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

    public function test_penyuluh_can_update_an_alsintan_with_a_camera_photo(): void
    {
        Storage::fake('public');
        $penyuluh = User::factory()->create(['role' => 'penyuluh']);
        $alsintan = $this->createAlsintan();

        $response = $this->actingAs($penyuluh)->put(route('alsintans.update', $alsintan), [
            'condition' => 'Rusak Ringan',
            'camera_photo' => UploadedFile::fake()->image('foto-kamera.jpg')->size(500),
        ]);

        $response->assertRedirect(route('alsintans.index'));
        $this->assertSame('Rusak Ringan', $alsintan->refresh()->condition);
        $this->assertNotNull($alsintan->photo_path);
        Storage::disk('public')->assertExists($alsintan->photo_path);
    }

    public function test_penyuluh_can_update_an_alsintan_with_a_photo_from_the_phone(): void
    {
        Storage::fake('public');
        $penyuluh = User::factory()->create(['role' => 'penyuluh']);
        $alsintan = $this->createAlsintan();

        $response = $this->actingAs($penyuluh)->put(route('alsintans.update', $alsintan), [
            'condition' => 'Baik',
            'photo' => UploadedFile::fake()->image('foto-galeri.png')->size(500),
        ]);

        $response->assertRedirect(route('alsintans.index'));
        $this->assertNotNull($alsintan->refresh()->photo_path);
        Storage::disk('public')->assertExists($alsintan->photo_path);
    }

    private function createAlsintan(): Alsintan
    {
        return Alsintan::query()->create([
            'type' => 'Traktor Roda Dua',
            'brand_type' => 'Quick G1000',
            'inventory_number' => 'ALS-2026-001',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
        ]);
    }
}
