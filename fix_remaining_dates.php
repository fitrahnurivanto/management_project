<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing remaining 6 projects - add 1 year to end_date:\n\n";

$projects = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->get();

foreach($projects as $project) {
    echo "Fix: {$project->project_code} - {$project->project_name}\n";
    echo "  Start: {$project->start_date->format('d M Y')}\n";
    echo "  End:   {$project->end_date->format('d M Y')} → ";
    
    $newEndDate = $project->end_date->copy()->addYear();
    echo "{$newEndDate->format('d M Y')}\n\n";
    
    $project->end_date = $newEndDate;
    $project->save();
}

echo "Final verification:\n";
$remaining = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->count();

echo "Projects with end_date < start_date: {$remaining}\n";

if ($remaining == 0) {
    echo "\n✅ SUCCESS! All projects have valid dates now.\n";
}
