<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Project Filter for Admin Agency...\n\n";

// Simulate admin agency filter
$query = App\Models\Project::with(['client', 'order']);

// Admin agency filter
echo "Applying agency admin filter...\n";
$query->whereHas('order', function($q) {
    $q->where('division', 'agency');
});

// Filter year 2023
echo "Applying year 2023 filter...\n";
$query->whereYear(DB::raw('COALESCE(start_date, created_at)'), 2023);

// Get count
$count = $query->count();
echo "Total projects found: {$count}\n\n";

// Get the projects
$projects = $query->get();

foreach ($projects as $project) {
    echo "- {$project->project_name} ({$project->project_code})\n";
    echo "  Status: {$project->status}\n";
    echo "  Year: " . ($project->start_date ?? $project->created_at)->format('Y') . "\n";
    echo "  Order Division: " . ($project->order->division ?? 'NULL') . "\n\n";
}

// Check specifically for PRJ-20230718-0023
echo "\n" . str_repeat("=", 50) . "\n";
echo "Checking specifically PRJ-20230718-0023...\n\n";

$specificQuery = App\Models\Project::where('project_code', 'PRJ-20230718-0023');
$specificQuery->whereHas('order', function($q) {
    $q->where('division', 'agency');
});
$specificQuery->whereYear(DB::raw('COALESCE(start_date, created_at)'), 2023);

$found = $specificQuery->exists();
echo "Found with agency filter + year 2023: " . ($found ? "YES" : "NO") . "\n";
