<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "DATABASE DATE AUDIT - Checking all projects\n";
echo str_repeat("=", 100) . "\n\n";

$allProjects = App\Models\Project::with(['client', 'order'])
    ->whereNotNull('start_date')
    ->orderBy('project_code')
    ->get();

echo "Total projects in database: " . $allProjects->count() . "\n\n";

$issues = [];

foreach ($allProjects as $project) {
    $hasIssue = false;
    $issueDesc = [];
    
    // Extract year from project code
    $codeYear = null;
    if (preg_match('/PRJ-(\d{4})/', $project->project_code, $matches)) {
        $codeYear = (int)$matches[1];
    }
    
    $startYear = $project->start_date ? $project->start_date->year : null;
    $endYear = $project->end_date ? $project->end_date->year : null;
    
    // Check 1: Start date year should match project code year (or close)
    if ($codeYear && $startYear) {
        $yearDiff = abs($codeYear - $startYear);
        if ($yearDiff > 1) {
            $hasIssue = true;
            $issueDesc[] = "Start year ($startYear) too far from code year ($codeYear)";
        }
    }
    
    // Check 2: End date should be after start date
    if ($project->start_date && $project->end_date) {
        if ($project->end_date < $project->start_date) {
            $hasIssue = true;
            $issueDesc[] = "End date before start date";
        }
        
        // Check 3: Duration check - if it's supposed to be short term (1-3 months), years should match or be +1
        $diffMonths = $project->start_date->diffInMonths($project->end_date);
        if ($diffMonths <= 12) {
            // Short term project
            if ($endYear && $startYear) {
                $expectedEndYear = $startYear;
                // If start is in late year (Oct-Dec) and duration is 1-3 months, end could be next year
                if ($project->start_date->month >= 10 && $diffMonths <= 6) {
                    $expectedEndYear = $startYear + 1;
                }
                
                // But if end year is 2025 and start is 2023, that's wrong for short projects
                if (abs($endYear - $startYear) > 1 && $diffMonths <= 6) {
                    $hasIssue = true;
                    $issueDesc[] = "Duration only {$diffMonths} months but years differ by " . abs($endYear - $startYear);
                }
            }
        }
    }
    
    // Check 4: Start date year 2025 but code year 2023/2024
    if ($codeYear && $codeYear < 2025 && $startYear == 2025) {
        $hasIssue = true;
        $issueDesc[] = "Start date year 2025 but project code from {$codeYear}";
    }
    
    if ($hasIssue) {
        echo "❌ {$project->project_code} - {$project->project_name}\n";
        echo "   Start: " . ($project->start_date ? $project->start_date->format('d M Y') : 'NULL') . "\n";
        echo "   End:   " . ($project->end_date ? $project->end_date->format('d M Y') : 'NULL') . "\n";
        echo "   Order: " . ($project->order && $project->order->order_date ? $project->order->order_date : 'N/A') . "\n";
        foreach ($issueDesc as $desc) {
            echo "   • {$desc}\n";
        }
        
        // Suggest fix
        if ($codeYear && $startYear != $codeYear && $startYear == 2025) {
            $correctStart = $project->start_date->copy()->year($codeYear);
            echo "   → Suggested fix: Change start_date year from {$startYear} to {$codeYear}\n";
            echo "      New start: {$correctStart->format('d M Y')}\n";
        }
        
        echo "\n";
        
        $issues[] = [
            'project' => $project,
            'issues' => $issueDesc
        ];
    }
}

echo str_repeat("=", 100) . "\n";
echo "Total issues found: " . count($issues) . "\n\n";

if (count($issues) > 0) {
    echo "Do you want to fix these issues? (This will update the database)\n";
}
