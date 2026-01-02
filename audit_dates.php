<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Read CSV file
$csvFile = __DIR__ . '/3. Tabel Project (2).csv';
$handle = fopen($csvFile, 'r');

// Skip header
fgetcsv($handle, 0, ';');

echo "CSV vs DATABASE AUDIT\n";
echo str_repeat("=", 100) . "\n\n";

$issues = [];
$lineNumber = 2; // Start from line 2 (after header)

while (($data = fgetcsv($handle, 0, ';')) !== false && $lineNumber <= 131) {
    // Skip empty lines or lines with no PKS
    if (empty($data[1]) || trim($data[1]) == '') {
        $lineNumber++;
        continue;
    }
    
    $pksNumber = trim($data[1]);
    $clientName = trim($data[2]);
    $projectName = trim($data[9]);
    $startDateCSV = trim($data[10]);
    $durationCSV = trim($data[11]);
    $endDateCSV = trim($data[12]);
    
    // Skip if no project name
    if (empty($projectName)) {
        $lineNumber++;
        continue;
    }
    
    echo "Line {$lineNumber}: {$projectName}\n";
    echo "  CSV Data:\n";
    echo "    Client: {$clientName}\n";
    echo "    PKS: {$pksNumber}\n";
    echo "    Start: {$startDateCSV}\n";
    echo "    Duration: {$durationCSV}\n";
    echo "    End: {$endDateCSV}\n";
    
    // Find project in database by PKS or project name
    $project = null;
    if (!empty($pksNumber)) {
        $project = App\Models\Project::where('pks_number', $pksNumber)->first();
    }
    
    if (!$project && !empty($projectName)) {
        // Try to find by project name and client
        $project = App\Models\Project::where('project_name', 'LIKE', '%' . $projectName . '%')
            ->whereHas('client', function($q) use ($clientName) {
                $q->where('company_name', 'LIKE', '%' . $clientName . '%');
            })
            ->first();
    }
    
    if ($project) {
        echo "  Database:\n";
        echo "    Code: {$project->project_code}\n";
        echo "    Start: " . ($project->start_date ? $project->start_date->format('d M Y') : 'NULL') . "\n";
        echo "    End: " . ($project->end_date ? $project->end_date->format('d M Y') : 'NULL') . "\n";
        
        // Parse CSV dates
        $csvStartParsed = null;
        $csvEndParsed = null;
        
        try {
            if (!empty($startDateCSV) && $startDateCSV != '-') {
                // Try various date formats
                $csvStartParsed = \Carbon\Carbon::parse($startDateCSV);
            }
            
            if (!empty($endDateCSV) && $endDateCSV != '-') {
                $csvEndParsed = \Carbon\Carbon::parse($endDateCSV);
            }
        } catch (\Exception $e) {
            echo "    ⚠️ Cannot parse CSV date: " . $e->getMessage() . "\n";
        }
        
        // Compare dates
        $hasIssue = false;
        $issueDesc = [];
        
        if ($csvStartParsed && $project->start_date) {
            if ($csvStartParsed->format('Y-m-d') != $project->start_date->format('Y-m-d')) {
                $hasIssue = true;
                $issueDesc[] = "START DATE mismatch: CSV={$csvStartParsed->format('d M Y')} vs DB={$project->start_date->format('d M Y')}";
            }
        }
        
        if ($csvEndParsed && $project->end_date) {
            if ($csvEndParsed->format('Y-m-d') != $project->end_date->format('Y-m-d')) {
                $hasIssue = true;
                $issueDesc[] = "END DATE mismatch: CSV={$csvEndParsed->format('d M Y')} vs DB={$project->end_date->format('d M Y')}";
            }
        }
        
        if ($hasIssue) {
            echo "  ❌ ISSUE FOUND:\n";
            foreach ($issueDesc as $desc) {
                echo "      " . $desc . "\n";
            }
            
            $issues[] = [
                'line' => $lineNumber,
                'project_code' => $project->project_code,
                'project_name' => $projectName,
                'csv_start' => $csvStartParsed ? $csvStartParsed->format('Y-m-d') : null,
                'csv_end' => $csvEndParsed ? $csvEndParsed->format('Y-m-d') : null,
                'db_start' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                'db_end' => $project->end_date ? $project->end_date->format('Y-m-d') : null,
                'issues' => $issueDesc
            ];
        } else {
            echo "  ✅ OK\n";
        }
    } else {
        echo "  ⚠️ NOT FOUND in database\n";
    }
    
    echo "\n";
    $lineNumber++;
}

fclose($handle);

echo "\n" . str_repeat("=", 100) . "\n";
echo "SUMMARY: Found " . count($issues) . " issues\n\n";

if (count($issues) > 0) {
    echo "ISSUES DETAIL:\n";
    foreach ($issues as $issue) {
        echo "Line {$issue['line']} - {$issue['project_code']} - {$issue['project_name']}\n";
        foreach ($issue['issues'] as $desc) {
            echo "  • {$desc}\n";
        }
        if ($issue['csv_start']) {
            echo "  → Fix: Set start_date to {$issue['csv_start']}\n";
        }
        if ($issue['csv_end']) {
            echo "  → Fix: Set end_date to {$issue['csv_end']}\n";
        }
        echo "\n";
    }
}
