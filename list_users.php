<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DAFTAR SEMUA USER ===\n\n";

$users = DB::table('users')->get();

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nama: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Division: " . ($user->division ?? 'N/A') . "\n";
    echo "Password: [ENCRYPTED - tidak bisa dilihat plaintext]\n";
    echo "Created: {$user->created_at}\n";
    echo str_repeat('-', 50) . "\n\n";
}

echo "\n=== INFO PASSWORD ===\n";
echo "Password di database tersimpan dalam bentuk HASH (bcrypt)\n";
echo "Tidak bisa dilihat dalam bentuk plaintext karena alasan keamanan\n\n";

echo "Untuk reset password user, gunakan:\n";
echo "php artisan tinker\n";
echo "User::find(ID)->update(['password' => Hash::make('password_baru')]);\n";
