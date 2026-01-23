<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Client;
use App\Models\User;

echo "=== UPDATE EMAIL CLIENT ===\n\n";

// Cari client berdasarkan email lama
$oldEmail = 'yantifahri137@gmail.com';
$client = Client::where('email', $oldEmail)->first();

if (!$client) {
    echo "❌ Client dengan email {$oldEmail} tidak ditemukan.\n";
    exit;
}

echo "✓ Client ditemukan:\n";
echo "  ID: {$client->id}\n";
echo "  Nama: {$client->name}\n";
echo "  Email Client: {$client->email}\n";
echo "  User ID: {$client->user_id}\n";

if ($client->user) {
    echo "  Email User: {$client->user->email}\n";
}

echo "\n";
echo "Masukkan email baru: ";
$newEmail = trim(fgets(STDIN));

if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Email tidak valid!\n";
    exit;
}

echo "\n📝 Akan mengupdate:\n";
echo "  - Email di tabel clients: {$client->email} → {$newEmail}\n";

if ($client->user) {
    echo "  - Email di tabel users: {$client->user->email} → {$newEmail}\n";
}

echo "\nLanjutkan? (y/n): ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) !== 'y') {
    echo "❌ Dibatalkan.\n";
    exit;
}

try {
    // Update email di client
    $client->update(['email' => $newEmail]);
    echo "✅ Email di tabel clients berhasil diupdate!\n";
    
    // Update email di user jika ada
    if ($client->user) {
        $client->user->update(['email' => $newEmail]);
        echo "✅ Email di tabel users berhasil diupdate!\n";
    }
    
    echo "\n🎉 Update selesai!\n";
    echo "Email baru: {$newEmail}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
