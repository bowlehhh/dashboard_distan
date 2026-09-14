<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FormGuidanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function adminFormGuidance(): array
    {
        return [
            'alsintan' => ['alsintans.create', ['Contoh: Traktor Roda Dua', 'Opsional, contoh: ALS-2026-001', 'Pilih atau ambil foto kondisi alsintan']],
            'poktan' => ['poktans.create', ['Contoh: Tani Makmur', 'Contoh: 0812-3456-7890', 'Contoh: Jl. Pertanian No. 10']],
            'tanaman pangan' => ['crops.create', ['Contoh: Padi, Jagung, Kedelai', 'Contoh: 12.50', 'Contoh: Panen pertama']],
            'saprodi' => ['saprodis.create', ['Contoh: Pupuk Organik Granul', 'Contoh: Kg, Liter, Sak, atau Unit', 'Pilih foto produk']],
            'pengguna' => ['users.create', ['Contoh: Budi Santoso', 'Contoh: budi@distan.go.id', 'Minimal 8 karakter']],
            'pengaturan' => ['settings.index', ['Contoh: Dinas Pertanian Kabupaten Kutai Barat', 'Pilih file backup SIMANTAP', 'Masukkan kata sandi saat ini']],
        ];
    }

    #[DataProvider('adminFormGuidance')]
    public function test_admin_forms_render_contextual_input_guidance(string $routeName, array $guidance): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route($routeName));

        foreach ($guidance as $text) {
            $response->assertSee($text);
        }
    }

    public function test_penyuluh_alsintan_form_renders_field_input_guidance(): void
    {
        $penyuluh = User::factory()->create(['role' => 'penyuluh']);
        $alsintan = Alsintan::query()->create([
            'type' => 'Traktor Roda Dua',
            'brand_type' => 'Quick G1000',
            'inventory_number' => 'ALS-2026-001',
            'district' => 'Melak',
            'village' => 'Melak Ulu',
            'procurement_year' => 2026,
            'condition' => 'Baik',
            'usage_status' => 'Digunakan',
        ]);

        $response = $this->actingAs($penyuluh)->get(route('alsintans.edit', $alsintan));

        $response->assertSee('Ambil foto terbaru yang memperlihatkan kondisi alsintan.')
            ->assertSee('Ambil lokasi saat ini atau tempel link Google Maps');
    }

    public function test_penyuluh_harvest_form_renders_harvest_input_guidance(): void
    {
        $penyuluh = User::factory()->create(['role' => 'penyuluh']);
        $crop = Crop::query()->create([
            'commodity' => 'Padi',
            'district' => 'Melak',
            'planted_area' => 12.5,
            'harvested_area' => 10,
            'production' => '1500 Kg',
            'period' => '2026',
        ]);

        $response = $this->actingAs($penyuluh)->get(route('crops.edit', $crop));

        $response->assertSee('Contoh: 10.50')
            ->assertSee('Contoh: 1500 Kg')
            ->assertSee('Hasil panen musim pertama telah ditimbang dan diverifikasi.');
    }

    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function guestFormGuidance(): array
    {
        return [
            'login' => ['/masuk', ['nama@distan.go.id', 'Masukkan kata sandi']],
            'lupa password' => ['/lupa-password', ['nama@distan.go.id']],
            'reset password' => ['/reset-password/token-contoh?email=user@example.test', ['nama@distan.go.id', 'Minimal 8 karakter', 'Ketik ulang kata sandi baru']],
        ];
    }

    #[DataProvider('guestFormGuidance')]
    public function test_guest_forms_render_contextual_input_guidance(string $url, array $guidance): void
    {
        $response = $this->get($url);

        foreach ($guidance as $text) {
            $response->assertSee($text);
        }
    }
}
