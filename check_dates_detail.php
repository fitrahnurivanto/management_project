<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking projects with end_date < start_date (DETAILED):\n\n";

$projects = App\Models\Project::with('order')
    ->whereNotNull('start_date')
    ->whereNotNull('end_date')
    ->whereRaw('end_date < start_date')
    ->orderBy('project_code')
    ->get();

echo "Found {$projects->count()} projects with invalid dates:\n\n";

foreach($projects as $project) {
    $codeYear = null;
    if (preg_match('/PRJ-(\d{4})/', $project->project_code, $matches)) {
        $codeYear = $matches[1];
    }
    
    $orderDate = 'No Order';
    $orderYear = null;
    if ($project->order && $project->order->order_date) {
        $orderDateObj = is_string($project->order->order_date) ? 
            \Carbon\Carbon::parse($project->order->order_date) : 
            $project->order->order_date;
        $orderDate = $orderDateObj->format('d M Y');
        $orderYear = $orderDateObj->year;
    }
    
    echo str_repeat("=", 80) . "\n";
    echo "Project: {$project->project_name}\n";
    echo "Code: {$project->project_code} (Year in code: {$codeYear})\n";
    echo "Order Date: {$orderDate}\n";
    echo "Start Date: {$project->start_date->format('d M Y')} (Year: {$project->start_date->year})\n";
    echo "End Date:   {$project->end_date->format('d M Y')} (Year: {$project->end_date->year})\n";
    
    // Analysis
    echo "\nAnalysis:\n";
    if ($codeYear && $project->start_date->year != $codeYear) {
        echo "  ⚠️ START DATE SALAH! Project code menunjukkan {$codeYear}, tapi start_date {$project->start_date->year}\n";
        echo "  → Fix: Ubah start_date dari {$project->start_date->format('d M Y')} ke {$project->start_date->format('d M')} {$codeYear}\n";
    }
    
    if ($orderYear && $project->start_date->year != $orderYear) {
        echo "  ⚠️ START DATE vs ORDER tidak match! Order {$orderYear}, start_date {$project->start_date->year}\n";
    }
    
    if ($codeYear && $project->end_date->year > $codeYear + 1) {
        echo "  ❓ End date terlalu jauh dari project code year\n";
    }
    
    echo "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "KESIMPULAN:\n";
echo "Sepertinya yang SALAH adalah START_DATE, bukan END_DATE!\n";
echo "Banyak project code 2023 tapi start_date 2025.\n";
echo "Fix: Set start_date sesuai dengan year di project_code.\n";
