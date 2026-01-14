<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing CORRECT Client-User Relationship...\n\n";

// Check if user afa.nurkholik@gmail.com exists
$correctUser = App\Models\User::where('email', 'afa.nurkholik@gmail.com')->first();

if (!$correctUser) {
    echo "Creating user for Muhammad Wafa Nurkholi...\n";
    $correctUser = App\Models\User::create([
        'name' => 'Muhammad Wafa Nurkholi',
        'email' => 'afa.nurkholik@gmail.com',
        'password' => Hash::make('password123'),
        'role' => 'client',
    ]);
    echo "✓ User created (ID: {$correctUser->id})\n\n";
}

// Disconnect fitrah from client 21
$wrongUser = App\Models\User::where('email', 'fitrahnurivanto@gmail.com')->first();
$client21 = App\Models\Client::find(21);

if ($client21) {
    echo "Connecting CORRECT user to Client 21 (SMA SAINS AL QURAN):\n";
    echo "- Client: {$client21->company_name}\n";
    echo "- User: {$correctUser->name} ({$correctUser->email})\n\n";
    
    $client21->user_id = $correctUser->id;
    $client21->save();
    echo "✓ Connected!\n\n";
}

// Set fitrah back to null or correct client
if ($wrongUser) {
    $wrongClient = App\Models\Client::where('user_id', $wrongUser->id)->first();
    if ($wrongClient && $wrongClient->id != 21) {
        echo "Fitrah Nur Ivanto remains connected to Client: {$wrongClient->company_name}\n";
    } else {
        echo "Fitrah Nur Ivanto disconnected from wrong client\n";
    }
}

// Verify
echo "\nVerification:\n";
$client = App\Models\Client::find(21);
if ($client && $client->user_id) {
    $user = App\Models\User::find($client->user_id);
    echo "- Client: {$client->company_name}\n";
    echo "- User: {$user->name} ({$user->email})\n";
    echo "- Projects:\n";
    foreach ($client->projects as $project) {
        echo "    * {$project->project_name} ({$project->project_code})\n";
    }
}
