<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CEK DATA PROJECT JANUARI 2026 ===" . PHP_EOL . PHP_EOL;

// Cek dengan created_at
$countCreated = DB::table('projects')
    ->whereYear('created_at', 2026)
    ->whereMonth('created_at', 1)
    ->count();

echo "Project dengan created_at di Januari 2026: {$countCreated}" . PHP_EOL;

// Cek dengan start_date
$countStart = DB::table('projects')
    ->whereYear('start_date', 2026)
    ->whereMonth('start_date', 1)
    ->count();

echo "Project dengan start_date di Januari 2026: {$countStart}" . PHP_EOL;

// Cek dengan COALESCE (yang dipakai di grafik)
$countCoalesce = DB::table('projects')
    ->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2026])
    ->whereRaw('MONTH(COALESCE(start_date, created_at)) = ?', [1])
    ->count();

echo "Project dengan COALESCE di Januari 2026: {$countCoalesce}" . PHP_EOL . PHP_EOL;

// Tampilkan sample project 2026
echo "SAMPLE PROJECT 2026 (First 5):" . PHP_EOL;
$projects = DB::table('projects')
    ->whereRaw('YEAR(COALESCE(start_date, created_at)) >= ?', [2026])
    ->orderBy(DB::raw('COALESCE(start_date, created_at)'))
    ->limit(5)
    ->get(['id', 'project_name', 'start_date', 'created_at']);

if ($projects->count() > 0) {
    foreach ($projects as $p) {
        $date = $p->start_date ?? $p->created_at;
        echo "ID: {$p->id} | Date: {$date} | {$p->project_name}" . PHP_EOL;
    }
} else {
    echo "TIDAK ADA PROJECT DI 2026!" . PHP_EOL;
}

echo PHP_EOL . "TOTAL PROJECTS 2026: " . DB::table('projects')->whereRaw('YEAR(COALESCE(start_date, created_at)) = ?', [2026])->count() . PHP_EOL;
