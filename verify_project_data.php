<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFIKASI DATA PROJECT 2025 ===" . PHP_EOL . PHP_EOL;

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

echo "MENGGUNAKAN COALESCE(start_date, created_at):" . PHP_EOL;
for ($m = 1; $m <= 12; $m++) {
    $count = DB::table('projects')
        ->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2025])
        ->whereRaw('MONTH(COALESCE(start_date, created_at)) = ?', [$m])
        ->count();
    
    echo $months[$m-1] . ' 2025: ' . $count . ' project' . PHP_EOL;
}

echo PHP_EOL . "SAMPLE PROJECT DATA (First 10):" . PHP_EOL;
$projects = DB::table('projects')
    ->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2025])
    ->orderBy(DB::raw('COALESCE(start_date, created_at)'))
    ->limit(10)
    ->get(['id', 'project_name', 'start_date', 'created_at']);

foreach ($projects as $p) {
    $date = $p->start_date ?? $p->created_at;
    echo "ID: {$p->id} | {$p->project_name} | Date: {$date}" . PHP_EOL;
}

echo PHP_EOL . "TOTAL 2025: " . DB::table('projects')->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2025])->count() . " project" . PHP_EOL;
echo "TOTAL 2024: " . DB::table('projects')->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2024])->count() . " project" . PHP_EOL;
echo "TOTAL 2026: " . DB::table('projects')->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2026])->count() . " project" . PHP_EOL;
