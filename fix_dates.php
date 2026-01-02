<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing projects with end_date < start_date...\n\n";

// Get projects before fix
$beforeCount = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->count();

echo "Found {$beforeCount} projects to fix\n\n";

// Get the projects for logging
$projects = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->get();

$fixed = 0;
foreach($projects as $project) {
    $oldEndDate = $project->end_date->copy();
    $newEndDate = $project->end_date->addYear();
    
    echo "Fixing {$project->project_code}: ";
    echo "{$oldEndDate->format('d M Y')} → {$newEndDate->format('d M Y')}\n";
    
    $project->end_date = $newEndDate;
    $project->save();
    $fixed++;
}

// Verify fix
$afterCount = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->count();

echo "\n✓ Fixed {$fixed} projects\n";
echo "✓ Remaining invalid dates: {$afterCount}\n\n";

if($afterCount == 0) {
    echo "SUCCESS! All dates are now valid.\n";
} else {
    echo "WARNING: Some dates still invalid, need manual check.\n";
}
