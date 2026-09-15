<?php

namespace Tests\Feature;

use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use UnexpectedValueException;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_only_the_initial_admin_account(): void
    {
        config()->set('simantap.admin.email', 'admin@example.test');
        config()->set('simantap.admin.password', 'initial-password');

        $this->seed();

        $admin = User::query()->sole();

        $this->assertSame('admin@example.test', $admin->email);
        $this->assertTrue(Hash::check('initial-password', $admin->password));
        $this->assertSame(0, Poktan::query()->count());
        $this->assertSame(0, Alsintan::query()->count());
        $this->assertSame(0, Saprodi::query()->count());
        $this->assertSame(0, Crop::query()->count());
    }

    public function test_rerunning_seeder_preserves_the_existing_admin_password(): void
    {
        config()->set('simantap.admin.email', 'admin@example.test');
        config()->set('simantap.admin.password', 'configured-password');
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'existing-password',
            'role' => 'admin',
        ]);

        $this->seed();

        $this->assertTrue(Hash::check('existing-password', $admin->refresh()->password));
        $this->assertFalse(Hash::check('configured-password', $admin->password));
    }

    public function test_existing_admin_allows_seeder_to_run_without_admin_configuration(): void
    {
        config()->set('simantap.admin.email');
        config()->set('simantap.admin.password');
        $admin = User::factory()->create([
            'email' => 'existing-admin@example.test',
            'password' => 'existing-password',
            'role' => 'admin',
        ]);

        $this->seed();

        $this->assertSame(1, User::query()->count());
        $this->assertTrue(Hash::check('existing-password', $admin->refresh()->password));
    }

    public function test_seeder_requires_admin_configuration_when_no_admin_exists(): void
    {
        config()->set('simantap.admin.email');
        config()->set('simantap.admin.password');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('SIMANTAP_ADMIN_EMAIL harus berisi alamat email yang valid.');

        $this->seed();
    }
}
