<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_user_that_can_log_in(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Penyuluh Baru',
            'email' => 'penyuluh.baru@example.test',
            'role' => 'penyuluh',
            'unit_kerja' => 'Bidang Alsintan',
            'is_active' => '1',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ]);

        $response->assertRedirect(route('users.index'));
        $user = User::query()->where('email', 'penyuluh.baru@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('password-baru', $user->password));

        $this->post(route('login.store'), [
            'email' => 'penyuluh.baru@example.test',
            'password' => 'password-baru',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('page_loader', 'login');
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.test',
            'password' => 'password-baru',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password-baru',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_last_active_admin_cannot_remove_their_own_admin_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'pimpinan',
            'unit_kerja' => $admin->unit_kerja,
            'is_active' => '1',
        ]);

        $response->assertSessionHas('error', 'Minimal satu akun admin aktif harus tetap tersedia.');
        $this->assertSame('admin', $admin->refresh()->role);
    }
}
