<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PROJECT COUNT PER MONTH (2026) ===" . PHP_EOL . PHP_EOL;

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

for ($m = 1; $m <= 12; $m++) {
    $count = DB::table('projects')
        ->whereYear('created_at', 2026)
        ->whereMonth('created_at', $m)
        ->count();
    
    echo $months[$m-1] . ': ' . $count . ' project' . PHP_EOL;
}

echo PHP_EOL . "Total 2026: " . DB::table('projects')->whereYear('created_at', 2026)->count() . " project" . PHP_EOL;
echo "Total 2025: " . DB::table('projects')->whereYear('created_at', 2025)->count() . " project" . PHP_EOL;
echo "Total 2024: " . DB::table('projects')->whereYear('created_at', 2024)->count() . " project" . PHP_EOL;
