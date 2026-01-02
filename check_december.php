<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "CHECKING DECEMBER PROJECTS\n";
echo str_repeat("=", 100) . "\n\n";

// Get all projects with start_date in December
$decemberProjects = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereMonth('start_date', 12)
    ->orderBy('start_date')
    ->get();

echo "Found " . $decemberProjects->count() . " projects starting in December\n\n";

$issues = [];

foreach ($decemberProjects as $project) {
    $startYear = $project->start_date->year;
    $endYear = $project->end_date->year;
    $startMonth = $project->start_date->month;
    $endMonth = $project->end_date->month;
    
    $duration = $project->start_date->diffInDays($project->end_date);
    
    echo "{$project->project_code} - {$project->project_name}\n";
    echo "  Start: {$project->start_date->format('d M Y')} (Day {$project->start_date->day})\n";
    echo "  End:   {$project->end_date->format('d M Y')}\n";
    echo "  Duration: {$duration} days\n";
    
    // Check: If start in December and end in same December (same year)
    // and duration is more than 25 days, end should probably be next year
    if ($startMonth == 12 && $endMonth == 12 && $startYear == $endYear) {
        // This looks suspicious - a December to December project in same year
        // Calculate what end date should be based on start date + 30 days
        $expectedEnd = $project->start_date->copy()->addDays(30);
        
        if ($expectedEnd->year > $startYear) {
            echo "  ⚠️ ISSUE: End date should be in " . ($startYear + 1) . "\n";
            echo "  → Expected end (start + 30 days): {$expectedEnd->format('d M Y')}\n";
            
            $issues[] = [
                'project' => $project,
                'current_end' => $project->end_date->format('Y-m-d'),
                'suggested_end' => $expectedEnd->format('Y-m-d')
            ];
        } else {
            echo "  ✅ OK (short duration within December)\n";
        }
    }
    // Check: If start in late November/December and end in early next year
    else if ($startMonth == 12 && $endMonth == 1 && $endYear == $startYear) {
        echo "  ⚠️ ISSUE: End date Jan {$endYear} should be Jan " . ($startYear + 1) . "\n";
        
        $correctEnd = $project->end_date->copy()->year($startYear + 1);
        
        $issues[] = [
            'project' => $project,
            'current_end' => $project->end_date->format('Y-m-d'),
            'suggested_end' => $correctEnd->format('Y-m-d')
        ];
    }
    else {
        echo "  ✅ OK\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 100) . "\n";
echo "Total issues found: " . count($issues) . "\n\n";

if (count($issues) > 0) {
    echo "ISSUES SUMMARY:\n";
    foreach ($issues as $issue) {
        $proj = $issue['project'];
        echo "• {$proj->project_code}: {$issue['current_end']} → {$issue['suggested_end']}\n";
    }
    
    echo "\nFix these issues? (Will update database)\n";
}
