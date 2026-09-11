<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::select('email', 'role', 'is_active', 'name')->get();

echo "=== User yang terdaftar di database ===\n\n";

foreach ($users as $user) {
    echo "Name:    {$user->name}\n";
    echo "Email:   {$user->email}\n";
    echo "Role:    {$user->role}\n";
    echo "Active:  " . ($user->is_active ? "Ya" : "Tidak") . "\n";
    echo "------------------------\n";
}

echo "\nTotal user: " . $users->count() . "\n";
