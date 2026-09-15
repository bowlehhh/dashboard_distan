<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use UnexpectedValueException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::query()->where('role', 'admin')->exists()) {
            $this->command?->info('Akun admin sudah tersedia. Seeder tidak mengubah akun atau password yang ada.');

            return;
        }

        $adminEmail = config('simantap.admin.email');
        $adminPassword = config('simantap.admin.password');

        if (! is_string($adminEmail) || filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false) {
            throw new UnexpectedValueException('SIMANTAP_ADMIN_EMAIL harus berisi alamat email yang valid.');
        }

        if (! is_string($adminPassword) || mb_strlen($adminPassword) < 12) {
            throw new UnexpectedValueException('SIMANTAP_ADMIN_PASSWORD harus berisi minimal 12 karakter.');
        }

        User::query()->firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin SIMANTAP',
                'password' => $adminPassword,
                'role' => 'admin',
                'unit_kerja' => 'Dinas Pertanian',
                'is_active' => true,
            ],
        );
    }
}
