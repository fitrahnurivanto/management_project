<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking User-Client Relationship after logout/login...\n\n";

// Get user fitrah
$user = App\Models\User::where('email', 'fitrahnurivanto@gmail.com')->first();

if ($user) {
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n\n";
    
    if ($user->client) {
        echo "Connected to Client:\n";
        echo "- ID: {$user->client->id}\n";
        echo "- Company: {$user->client->company_name}\n\n";
        
        echo "Client's Projects:\n";
        $projects = App\Models\Project::where('client_id', $user->client->id)->get();
        foreach ($projects as $project) {
            echo "- {$project->project_name} ({$project->project_code})\n";
        }
        
        echo "\n\nProjects from Orders (query yang sebenarnya dipakai):\n";
        $orderProjects = App\Models\Project::whereHas('order', function($q) use ($user) {
            $q->where('client_id', $user->client->id);
        })->get();
        
        foreach ($orderProjects as $project) {
            echo "- {$project->project_name} ({$project->project_code})\n";
            echo "  Order ID: {$project->order_id}\n";
            echo "  Order Client ID: {$project->order->client_id}\n";
        }
    } else {
        echo "✗ No client connected!\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n\n";

// Check project PRJ-20230718-0023
$project = App\Models\Project::where('project_code', 'PRJ-20230718-0023')->first();
if ($project) {
    echo "Project PRJ-20230718-0023:\n";
    echo "- client_id (project table): {$project->client_id}\n";
    echo "- order_id: {$project->order_id}\n";
    if ($project->order) {
        echo "- order client_id: {$project->order->client_id}\n";
        $orderClient = App\Models\Client::find($project->order->client_id);
        echo "- order client name: {$orderClient->company_name}\n";
    }
}
