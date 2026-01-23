<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== CEK SEMUA USER DI DATABASE ===\n\n";

$users = DB::select('SELECT id, name, email, role, google_id FROM users ORDER BY id');

if (empty($users)) {
    echo "Tidak ada user di database.\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Nama: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: {$user->role}\n";
        echo "Google ID: " . ($user->google_id ? $user->google_id : 'Belum link') . "\n";
        echo "-----------------------------------\n";
    }
    
    echo "\nTotal: " . count($users) . " users\n";
}

echo "\n=== CEK SESSION SETTINGS ===\n\n";
echo "SESSION_DRIVER: " . env('SESSION_DRIVER') . "\n";
echo "SESSION_LIFETIME: " . env('SESSION_LIFETIME') . " menit\n";

echo "\n";
