<?php

namespace Tests\Feature;

use App\Models\Poktan;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('settings.index'));

        $response->assertSee('Profil Instansi');
        $response->assertDontSee('Notifikasi');
    }

    public function test_operator_cannot_access_settings(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator)->get(route('settings.index'))->assertForbidden();
    }

    public function test_admin_can_update_institution_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('settings.profile.update'), [
            'name' => 'Dinas Pertanian Kutai Barat',
            'address' => 'Barong Tongkok',
            'phone' => '08123456789',
            'email' => 'distan@example.test',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('Dinas Pertanian Kutai Barat', SystemSetting::query()->where('key', 'profile')->firstOrFail()->value['name']);
    }

    public function test_admin_can_download_operational_data_backup(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Poktan::factory()->create(['name' => 'Tani Sejahtera']);

        $response = $this->actingAs($admin)->get(route('settings.backup.download'));

        $response->assertDownload();
        $this->assertStringContainsString('Tani Sejahtera', $response->streamedContent());
    }

    public function test_admin_can_update_appearance_preference(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('settings.appearance.update'), [
            'density' => 'bright',
        ]);

        $response->assertSessionHas('success', 'Preferensi tampilan berhasil disimpan.');
        $this->assertSame('bright', SystemSetting::query()->where('key', 'appearance')->firstOrFail()->value['density']);
        $this->actingAs($admin)->get(route('settings.index'))->assertSee('value="bright"');
    }

    public function test_admin_can_change_password_with_current_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $response = $this->actingAs($admin)->put(route('settings.security.update'), [
            'user_id' => $admin->id,
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'logout_other_sessions' => '1',
        ]);

        $response->assertSessionHas('success', 'Kata sandi berhasil diperbarui.');
        $this->assertTrue(Hash::check('new-password', $admin->refresh()->password));
    }

    public function test_admin_can_change_password_for_a_selected_user_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create([
            'email' => 'operator@example.test',
            'password' => 'password',
        ]);

        $response = $this->actingAs($admin)->put(route('settings.security.update'), [
            'user_id' => $operator->id,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHas('success', 'Kata sandi berhasil diperbarui.');
        $this->assertTrue(Hash::check('new-password', $operator->refresh()->password));
    }

    public function test_invalid_settings_input_is_rejected_and_reported(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('settings.appearance.update'), [
            'density' => 'invalid',
        ]);

        $response->assertSessionHasErrors('density');
        $this->assertDatabaseMissing('system_settings', ['key' => 'appearance']);
    }
}
