<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "FIXING PROJECT DATES based on Project Code Year\n";
echo str_repeat("=", 100) . "\n\n";

$allProjects = App\Models\Project::with(['client', 'order'])
    ->whereNotNull('start_date')
    ->orderBy('project_code')
    ->get();

$fixed = 0;

foreach ($allProjects as $project) {
    // Extract year from project code
    $codeYear = null;
    if (preg_match('/PRJ-(\d{4})/', $project->project_code, $matches)) {
        $codeYear = (int)$matches[1];
    }
    
    if (!$codeYear) continue;
    
    $startYear = $project->start_date ? $project->start_date->year : null;
    $endYear = $project->end_date ? $project->end_date->year : null;
    
    // Fix start_date if year is wrong
    if ($startYear && $startYear != $codeYear && abs($startYear - $codeYear) > 1) {
        $oldStart = $project->start_date->format('d M Y');
        $newStart = $project->start_date->copy()->year($codeYear);
        
        echo "Fix Start: {$project->project_code}\n";
        echo "  {$oldStart} → {$newStart->format('d M Y')}\n";
        
        $project->start_date = $newStart;
        
        // Also fix end_date if it exists and has similar issue
        if ($project->end_date && $endYear) {
            // If end year also wrong, try to fix it
            $expectedEndYear = $codeYear;
            
            // Calculate duration from start to end
            $duration = $project->start_date->diffInMonths($project->end_date);
            
            // If duration is less than 12 months, end should be same year or next year
            if ($duration <= 12) {
                $newEnd = $project->end_date->copy()->year($codeYear);
                
                // If end month is before start month, it's likely next year
                if ($newEnd->month < $newStart->month || 
                    ($newEnd->month == $newStart->month && $newEnd->day < $newStart->day)) {
                    $newEnd = $newEnd->addYear();
                }
                
                $oldEnd = $project->end_date->format('d M Y');
                echo "  End: {$oldEnd} → {$newEnd->format('d M Y')}\n";
                
                $project->end_date = $newEnd;
            }
        }
        
        $project->save();
        $fixed++;
        echo "\n";
    }
}

echo str_repeat("=", 100) . "\n";
echo "Fixed {$fixed} projects!\n\n";

// Final verification
echo "Verification - Projects with remaining issues:\n";
$remaining = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->count();

echo "Projects with end_date < start_date: {$remaining}\n";

// Check year mismatches
$yearMismatches = 0;
foreach (App\Models\Project::whereNotNull('start_date')->get() as $p) {
    if (preg_match('/PRJ-(\d{4})/', $p->project_code, $m)) {
        $codeYear = (int)$m[1];
        if (abs($p->start_date->year - $codeYear) > 1) {
            $yearMismatches++;
        }
    }
}

echo "Projects with year mismatch (>1 year diff): {$yearMismatches}\n";
