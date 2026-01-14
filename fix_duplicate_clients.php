<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Client;
use App\Models\User;

echo "=== Fixing Duplicate Clients ===\n\n";

// Get user fitrah
$user = User::where('email', 'fitrahnurivanto@gmail.com')->first();

if (!$user) {
    echo "User fitrahnurivanto@gmail.com not found!\n";
    exit;
}

echo "User ID: {$user->id}\n";
echo "User Email: {$user->email}\n\n";

// Get all clients with this email
$clients = Client::where('email', 'fitrahnurivanto@gmail.com')->get();

echo "Found {$clients->count()} clients with email fitrahnurivanto@gmail.com:\n\n";

foreach ($clients as $client) {
    echo "Client ID: {$client->id}\n";
    echo "Name: {$client->name}\n";
    echo "User ID: " . ($client->user_id ?? 'NULL') . "\n";
    
    $ordersCount = \App\Models\Order::where('client_id', $client->id)->count();
    echo "Orders: {$ordersCount}\n";
    
    if ($ordersCount > 0) {
        $orders = \App\Models\Order::where('client_id', $client->id)->get();
        foreach ($orders as $order) {
            echo "  - {$order->order_number}\n";
        }
    }
    echo "\n";
}

// Connect orphaned clients to user
echo "=== Fixing Connections ===\n\n";

Client::where('email', 'fitrahnurivanto@gmail.com')
    ->whereNull('user_id')
    ->update(['user_id' => $user->id]);

echo "Connected all orphaned clients with email fitrahnurivanto@gmail.com to User ID {$user->id}\n";

// Show result
echo "\n=== Result ===\n\n";
$clients = Client::where('email', 'fitrahnurivanto@gmail.com')->get();

foreach ($clients as $client) {
    echo "Client ID: {$client->id} ({$client->name}) - User ID: {$client->user_id}\n";
}
