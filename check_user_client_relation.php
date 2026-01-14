<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking User and Client Relationship...\n\n";

// Get user fitrah
$user = App\Models\User::where('email', 'fitrahnurivanto@gmail.com')->first();

if ($user) {
    echo "User Found:\n";
    echo "- ID: {$user->id}\n";
    echo "- Name: {$user->name}\n";
    echo "- Email: {$user->email}\n";
    echo "- Role: {$user->role}\n\n";
    
    if ($user->client) {
        echo "Connected to Client:\n";
        echo "- Client ID: {$user->client->id}\n";
        echo "- Company: {$user->client->company_name}\n";
        echo "- Contact Person: {$user->client->contact_person}\n";
        echo "- Email: {$user->client->contact_person}\n\n";
        
        // Check orders
        $orders = $user->client->orders;
        echo "Total Orders: {$orders->count()}\n\n";
        
        foreach ($orders as $order) {
            echo "Order: {$order->order_number}\n";
            if ($order->project) {
                echo "  Project: {$order->project->project_name} ({$order->project->project_code})\n";
            }
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n\n";

// Check the specific project
$project = App\Models\Project::where('project_code', 'PRJ-20230718-0023')->first();

if ($project && $project->client) {
    echo "Project PRJ-20230718-0023:\n";
    echo "- Client ID: {$project->client_id}\n";
    echo "- Company: {$project->client->company_name}\n";
    echo "- Contact Person: {$project->client->contact_person}\n";
    echo "- Contact Email: (not in clients table structure)\n";
    
    // Check if this client has a user
    $clientUser = App\Models\User::where('id', $project->client->user_id)->first();
    if ($clientUser) {
        echo "- User ID: {$clientUser->id}\n";
        echo "- User Name: {$clientUser->name}\n";
        echo "- User Email: {$clientUser->email}\n";
    } else {
        echo "- No user connected to this client\n";
    }
}
