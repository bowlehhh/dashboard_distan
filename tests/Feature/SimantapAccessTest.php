<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SimantapAccessTest extends TestCase
{
    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_logout_to_the_landing_page(): void
    {
        $user = User::factory()->make(['remember_token' => null]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('page_loader', 'logout');

        $this->assertGuest();
    }
}
