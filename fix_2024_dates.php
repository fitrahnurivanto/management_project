<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "FIXING 2024 PROJECT DATES\n";
echo str_repeat("=", 100) . "\n\n";

// Get all 2024 projects with wrong year
$projects2024 = App\Models\Project::where('project_code', 'LIKE', 'PRJ-2024%')
    ->whereNotNull('start_date')
    ->get();

$fixed = 0;

foreach ($projects2024 as $project) {
    $startYear = $project->start_date->year;
    
    // If start year is 2025 but should be 2024
    if ($startYear == 2025) {
        $oldStart = $project->start_date->format('d M Y');
        $newStart = $project->start_date->copy()->year(2024);
        
        echo "Fix: {$project->project_code} - {$project->project_name}\n";
        echo "  Start: {$oldStart} → {$newStart->format('d M Y')}\n";
        
        $project->start_date = $newStart;
        
        // Fix end_date too if exists
        if ($project->end_date && $project->end_date->year == 2025) {
            $oldEnd = $project->end_date->format('d M Y');
            $newEnd = $project->end_date->copy()->year(2024);
            
            // Check if end should be 2025 (if project crosses year boundary)
            if ($newEnd->month < $newStart->month) {
                $newEnd = $newEnd->year(2025);
            }
            
            echo "  End:   {$oldEnd} → {$newEnd->format('d M Y')}\n";
            $project->end_date = $newEnd;
        }
        
        $project->save();
        $fixed++;
        echo "\n";
    }
}

echo str_repeat("=", 100) . "\n";
echo "Fixed {$fixed} projects from 2024!\n\n";

// Final check
echo "Final verification:\n";
$issues = 0;
foreach (App\Models\Project::whereNotNull('start_date')->get() as $p) {
    if (preg_match('/PRJ-(\d{4})/', $p->project_code, $m)) {
        $codeYear = (int)$m[1];
        if ($p->start_date->year == 2025 && $codeYear < 2025) {
            $issues++;
            echo "Still wrong: {$p->project_code} start={$p->start_date->format('Y-m-d')}\n";
        }
    }
}

echo "\nRemaining issues: {$issues}\n";

if ($issues == 0) {
    echo "\n✅ ALL DATES FIXED! Semua project sudah sesuai tahun di project_code.\n";
}
