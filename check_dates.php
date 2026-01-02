<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking projects with end_date < start_date:\n\n";

$projects = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->orderBy('start_date', 'desc')
    ->get();

echo "Found {$projects->count()} projects with invalid dates:\n\n";

foreach($projects as $project) {
    echo "Project: {$project->project_name} ({$project->project_code})\n";
    echo "  Start: {$project->start_date->format('d M Y')}\n";
    echo "  End:   {$project->end_date->format('d M Y')}\n";
    
    // Calculate year difference
    $startYear = $project->start_date->year;
    $endYear = $project->end_date->year;
    $expectedEndYear = $startYear + 1;
    
    echo "  Issue: End date ({$endYear}) is before start date ({$startYear})\n";
    echo "  Fix: Change end_date year from {$endYear} to {$expectedEndYear}\n";
    echo "  New end date would be: " . $project->end_date->copy()->year($expectedEndYear)->format('d M Y') . "\n";
    echo "\n";
}

echo "\nSQL to fix:\n";
echo "UPDATE projects SET end_date = DATE_ADD(end_date, INTERVAL 1 YEAR) WHERE end_date < start_date;\n";
