<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Rollback previous fix (subtract 1 year from end_date):\n\n";

$result = DB::statement("UPDATE projects SET end_date = DATE_SUB(end_date, INTERVAL 1 YEAR) WHERE end_date > start_date AND YEAR(start_date) = 2025 AND YEAR(end_date) = 2026");

$affected = DB::table('projects')
    ->where('end_date', '>', DB::raw('start_date'))
    ->where(DB::raw('YEAR(start_date)'), '=', 2025)
    ->where(DB::raw('YEAR(end_date)'), '=', 2026)
    ->count();

echo "Rollback complete. Check remaining issues:\n\n";

$projects = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->count();

echo "Projects with end_date < start_date: {$projects}\n\n";

echo "Now fixing START_DATE based on project_code year:\n\n";

$projects = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->get();

$fixed = 0;
foreach($projects as $project) {
    if (preg_match('/PRJ-(\d{4})/', $project->project_code, $matches)) {
        $codeYear = $matches[1];
        $currentStartYear = $project->start_date->year;
        
        if ($codeYear != $currentStartYear) {
            // Calculate year difference
            $yearDiff = $currentStartYear - $codeYear;
            
            // Subtract years from start_date
            $newStartDate = $project->start_date->copy()->subYears($yearDiff);
            
            echo "Fix: {$project->project_code}\n";
            echo "  Start: {$project->start_date->format('d M Y')} → {$newStartDate->format('d M Y')}\n";
            
            $project->start_date = $newStartDate;
            $project->save();
            $fixed++;
        }
    }
}

echo "\nFixed {$fixed} projects!\n\n";

echo "Final check - projects with end_date < start_date:\n";
$remaining = App\Models\Project::whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->count();

echo "Remaining: {$remaining} projects\n";
