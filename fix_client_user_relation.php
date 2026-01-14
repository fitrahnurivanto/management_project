<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing Client-User Relationship...\n\n";

// Disconnect user 21 from client 63
$client63 = App\Models\Client::find(63);
if ($client63) {
    echo "Disconnecting User 21 from Client 63 (kae kopi)...\n";
    $client63->user_id = null;
    $client63->save();
    echo "✓ Done\n\n";
}

// Connect user 21 to client 21 (SMA SAINS AL QURAN)
$client21 = App\Models\Client::find(21);
if ($client21) {
    echo "Connecting User 21 to Client 21 (SMA SAINS AL QURAN)...\n";
    echo "- Company: {$client21->company_name}\n";
    $client21->user_id = 21;
    $client21->save();
    echo "✓ Done\n\n";
}

// Verify
$user = App\Models\User::find(21);
if ($user && $user->client) {
    echo "Verification:\n";
    echo "- User: {$user->name} ({$user->email})\n";
    echo "- Connected to Client: {$user->client->company_name} (ID: {$user->client->id})\n";
    echo "\nUser's projects:\n";
    foreach ($user->client->projects as $project) {
        echo "  - {$project->project_name} ({$project->project_code})\n";
    }
}
