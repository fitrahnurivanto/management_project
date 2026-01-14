<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== STRUKTUR TABEL CLIENTS ===\n\n";

$columns = DB::select("DESCRIBE clients");

foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type}) ";
    if ($column->Key == 'PRI') echo "[PRIMARY KEY] ";
    if ($column->Key == 'MUL') echo "[FOREIGN KEY] ";
    if ($column->Null == 'NO') echo "[NOT NULL] ";
    echo "\n";
}

echo "\n=== DATA CLIENT ===\n\n";
$clients = DB::table('clients')->get();
foreach ($clients as $client) {
    echo "ID: {$client->id}\n";
    echo "Name: {$client->name}\n";
    echo "Email: {$client->email}\n";
    if (isset($client->user_id)) {
        echo "User ID: {$client->user_id}\n";
    } else {
        echo "User ID: [KOLOM TIDAK ADA]\n";
    }
    echo str_repeat('-', 50) . "\n";
}
