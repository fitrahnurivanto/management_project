<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Client;

echo "=== KONEKSI USER KE CLIENT ===\n\n";

// Cari user dengan role client
$clientUsers = User::where('role', 'client')->get();

echo "Ditemukan {$clientUsers->count()} user dengan role client\n\n";

foreach ($clientUsers as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    
    // Cari client dengan email yang sama
    $client = Client::where('email', $user->email)->first();
    
    if ($client) {
        if ($client->user_id == $user->id) {
            echo "✓ Sudah terkoneksi dengan Client ID: {$client->id}\n";
        } else {
            // Update user_id
            $client->user_id = $user->id;
            $client->save();
            echo "✓ Berhasil dikoneksi dengan Client ID: {$client->id}\n";
        }
    } else {
        echo "✗ Tidak ada client dengan email yang sama\n";
    }
    
    echo str_repeat('-', 50) . "\n\n";
}

echo "=== SELESAI ===\n";
echo "Silakan refresh browser dan login lagi\n";
