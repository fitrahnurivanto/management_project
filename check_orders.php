<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\Client;

echo "=== Checking Orders ===\n\n";

$orderCodes = ['ORD-20260108-0004', 'ORD-20260107-0002', 'ORD-20260107-0001'];

foreach ($orderCodes as $code) {
    $order = Order::where('order_number', $code)->with('client.user')->first();
    
    if ($order) {
        echo "Order: {$order->order_number}\n";
        echo "Client ID: {$order->client_id}\n";
        echo "Client Name: {$order->client->name}\n";
        echo "Client Email: {$order->client->email}\n";
        echo "Client Phone: {$order->client->phone}\n";
        
        if ($order->client->user) {
            echo "Connected User ID: {$order->client->user_id}\n";
            echo "Connected User Email: {$order->client->user->email}\n";
        } else {
            echo "No connected user\n";
        }
        
        echo "Division: {$order->division}\n";
        echo "\n";
    }
}

echo "\n=== Client 63 (kae kopi) ===\n";
$client63 = Client::with('user')->find(63);
if ($client63) {
    echo "Name: {$client63->name}\n";
    echo "Email: {$client63->email}\n";
    echo "User ID: {$client63->user_id}\n";
    if ($client63->user) {
        echo "User Email: {$client63->user->email}\n";
    }
    
    $orders = Order::where('client_id', 63)->get();
    echo "Total Orders: {$orders->count()}\n";
    foreach ($orders as $order) {
        echo "  - {$order->order_number}\n";
    }
}
