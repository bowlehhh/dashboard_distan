<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_admin_can_create_a_user_that_can_log_in(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Operator Baru',
            'email' => 'operator.baru@example.test',
            'role' => 'operator',
            'unit_kerja' => 'Bidang Alsintan',
            'is_active' => '1',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ]);

        $response->assertRedirect(route('users.index'));
        $user = User::query()->where('email', 'operator.baru@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('password-baru', $user->password));

        $this->post(route('login.store'), [
            'email' => 'operator.baru@example.test',
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
}
