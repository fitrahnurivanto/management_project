<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking available clients for testing account...\n\n";

// Get user fitrah
$user = App\Models\User::where('email', 'fitrahnurivanto@gmail.com')->first();
echo "User: {$user->name} ({$user->email})\n";
echo "Current client: " . ($user->client ? $user->client->company_name : "NONE") . "\n\n";

// Get client 63 (kae kopi) - the original one
$client63 = App\Models\Client::find(63);
if ($client63) {
    echo "Option 1: Client ID 63\n";
    echo "- Company: {$client63->company_name}\n";
    echo "- Current user_id: " . ($client63->user_id ?? 'NULL') . "\n";
    echo "- Total Orders: " . $client63->orders->count() . "\n";
    echo "- Total Projects: " . $client63->projects->count() . "\n\n";
}

// Show clients without users
$clientsNoUser = App\Models\Client::whereNull('user_id')
    ->whereHas('orders')
    ->with('orders', 'projects')
    ->limit(5)
    ->get();

echo "Clients without user (available for connection):\n";
foreach ($clientsNoUser as $client) {
    echo "\nClient ID {$client->id}:\n";
    echo "- Company: {$client->company_name}\n";
    echo "- Orders: {$client->orders->count()}\n";
    echo "- Projects: {$client->projects->count()}\n";
}

echo "\n\nRecommendation: Connect fitrah to Client 63 (kae kopi) for testing?\n";
echo "Run: php connect_fitrah_to_client63.php\n";
