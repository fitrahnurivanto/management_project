<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Project PRJ-20230718-0023...\n\n";

$project = App\Models\Project::where('project_code', 'PRJ-20230718-0023')->first();

if ($project) {
    echo "✓ Project Found!\n";
    echo "- Name: {$project->project_name}\n";
    echo "- Code: {$project->project_code}\n";
    echo "- Order ID: {$project->order_id}\n";
    echo "- Start Date: " . ($project->start_date ?? 'NULL') . "\n";
    echo "- End Date: " . ($project->end_date ?? 'NULL') . "\n";
    echo "- Created At: {$project->created_at}\n";
    echo "- Status: {$project->status}\n\n";
    
    // Check what year filter would use
    $filterDate = $project->start_date ?? $project->created_at;
    echo "Filter uses: " . ($project->start_date ? 'start_date' : 'created_at') . "\n";
    echo "Year for filter: " . ($filterDate ? $filterDate->format('Y') : 'NULL') . "\n\n";
    
    if ($project->order) {
        echo "ORDER INFO:\n";
        echo "- Order Number: {$project->order->order_number}\n";
        echo "- Order Division: " . ($project->order->division ?? 'NULL') . "\n";
        echo "- Order Date: {$project->order->order_date}\n";
        
        if ($project->order->client) {
            echo "- Client: {$project->order->client->company_name}\n";
        }
    } else {
        echo "✗ Order not found!\n";
    }
} else {
    echo "✗ Project NOT FOUND in database\n";
}
