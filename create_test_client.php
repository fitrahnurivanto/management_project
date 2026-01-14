<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Client;

echo "=== MEMBUAT TEST CLIENT ===\n\n";

// Cari user dengan role client
$clientUsers = User::where('role', 'client')->get();

if ($clientUsers->count() == 0) {
    echo "Tidak ada user dengan role client. Membuat user baru...\n\n";
    
    // Buat user client baru
    $user = User::create([
        'name' => 'Test Client',
        'email' => 'client@test.com',
        'password' => bcrypt('password123'),
        'role' => 'client',
    ]);
    
    echo "User client baru dibuat:\n";
    echo "Email: client@test.com\n";
    echo "Password: password123\n\n";
    
    $clientUsers = collect([$user]);
}

echo "Ditemukan " . $clientUsers->count() . " user dengan role client:\n\n";

foreach ($clientUsers as $user) {
    echo "User ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    
    // Cek apakah sudah punya data client
    $client = Client::where('email', $user->email)->first();
    
    if (!$client) {
        echo "Status: BELUM ADA DATA CLIENT - Membuat data client...\n";
        
        $client = Client::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '081234567890',
            'company' => 'Test Company',
            'address' => 'Jl. Test No. 123',
        ]);
        
        echo "✓ Client berhasil dibuat dengan ID: {$client->id}\n";
    } else {
        echo "Status: ✓ SUDAH ADA DATA CLIENT (ID: {$client->id})\n";
    }
    
    echo str_repeat('-', 50) . "\n\n";
}

echo "\n=== SELESAI ===\n";
echo "Silakan login dengan email user client di atas\n";
echo "Password default: password123\n";
