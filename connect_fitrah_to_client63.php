<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Reconnecting fitrah to Client 63 (kae kopi) for testing...\n\n";

$user = App\Models\User::where('email', 'fitrahnurivanto@gmail.com')->first();
$client63 = App\Models\Client::find(63);

if ($client63) {
    $client63->user_id = $user->id;
    $client63->save();
    
    echo "✓ Connected!\n\n";
    echo "User: {$user->name} ({$user->email})\n";
    echo "Client: {$client63->company_name}\n";
    echo "Orders: {$client63->orders->count()}\n";
    
    if ($client63->orders->count() > 0) {
        foreach ($client63->orders as $order) {
            echo "\nOrder: {$order->order_number}\n";
            if ($order->project) {
                echo "  Project: {$order->project->project_name}\n";
            }
        }
    }
    
    echo "\n\n✓ Sekarang bisa login dengan fitrahnurivanto@gmail.com untuk testing!\n";
}
