<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "FIXING DECEMBER PROJECTS\n";
echo str_repeat("=", 100) . "\n\n";

$projectCodes = [
    'PRJ-20240115-0050',
    'PRJ-20240124-0051',
    'PRJ-20240130-0052',
    'PRJ-20240130-0053',
    'PRJ-20240502-0054',
    'PRJ-20241202-0055',
    'PRJ-20240223-0056'
];

$fixed = 0;

foreach ($projectCodes as $code) {
    $project = App\Models\Project::where('project_code', $code)->first();
    
    if (!$project) {
        echo "⚠️ Project {$code} not found\n";
        continue;
    }
    
    echo "Fix: {$project->project_code} - {$project->project_name}\n";
    echo "  Start: {$project->start_date->format('d M Y')}\n";
    echo "  Old End: {$project->end_date->format('d M Y')} (Duration: 0 days)\n";
    
    // Add 30 days to start date
    $newEnd = $project->start_date->copy()->addDays(30);
    
    echo "  New End: {$newEnd->format('d M Y')} (Duration: 30 days)\n";
    
    $project->end_date = $newEnd;
    $project->save();
    
    $fixed++;
    echo "  ✅ Fixed!\n\n";
}

echo str_repeat("=", 100) . "\n";
echo "Fixed {$fixed} projects!\n\n";

// Verify
echo "Verification:\n";
$stillIssues = App\Models\Project::whereMonth('start_date', 12)
    ->whereNotNull('end_date')
    ->whereRaw('DATE(start_date) = DATE(end_date)')
    ->count();

echo "Projects with start_date = end_date in December: {$stillIssues}\n";

if ($stillIssues == 0) {
    echo "\n✅ ALL DECEMBER PROJECTS FIXED!\n";
}
