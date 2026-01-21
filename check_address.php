<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Client;
use App\Models\Order;

echo "=== CEK DATA ALAMAT ===" . PHP_EOL . PHP_EOL;

// Cek Clients
echo "CLIENTS:" . PHP_EOL;
echo "Total clients: " . Client::count() . PHP_EOL;

$clientsWithAddress = Client::whereNotNull('address')
    ->where('address', '!=', '')
    ->where('address', '!=', 'N/A')
    ->count();
echo "Clients dengan alamat: " . $clientsWithAddress . PHP_EOL . PHP_EOL;

// Sample clients dengan alamat
echo "Sample clients dengan alamat:" . PHP_EOL;
$samples = Client::whereNotNull('address')
    ->where('address', '!=', '')
    ->where('address', '!=', 'N/A')
    ->take(5)
    ->get(['id', 'name', 'address', 'company_address']);

foreach($samples as $c) {
    echo "ID {$c->id}: {$c->name}" . PHP_EOL;
    echo "  - Address: " . substr($c->address, 0, 80) . PHP_EOL;
    if ($c->company_address) {
        echo "  - Company Address: " . substr($c->company_address, 0, 80) . PHP_EOL;
    }
    echo PHP_EOL;
}

// Cek Orders
echo "ORDERS:" . PHP_EOL;
echo "Total orders: " . Order::count() . PHP_EOL;

// Orders dimana client TIDAK punya alamat
$ordersWithoutClientAddress = Order::whereHas('client', function($q) {
    $q->where(function($q2) {
        $q2->whereNull('address')
           ->orWhere('address', '')
           ->orWhere('address', 'N/A');
    });
})->count();

echo "Orders dengan client TIDAK punya alamat: " . $ordersWithoutClientAddress . PHP_EOL;

// Orders dimana client PUNYA alamat
$ordersWithClientAddress = Order::whereHas('client', function($q) {
    $q->whereNotNull('address')
      ->where('address', '!=', '')
      ->where('address', '!=', 'N/A');
})->count();

echo "Orders dengan client PUNYA alamat: " . $ordersWithClientAddress . PHP_EOL . PHP_EOL;

// Sample orders tanpa alamat client
if ($ordersWithoutClientAddress > 0) {
    echo "Sample orders TANPA alamat client (butuh diupdate untuk PKS):" . PHP_EOL;
    $problemOrders = Order::whereHas('client', function($q) {
        $q->where(function($q2) {
            $q2->whereNull('address')
               ->orWhere('address', '')
               ->orWhere('address', 'N/A');
        });
    })->with('client:id,name,address')
      ->take(10)
      ->get(['id', 'order_number', 'client_id', 'payment_status']);

    foreach($problemOrders as $o) {
        echo "  Order {$o->order_number} (Status: {$o->payment_status})" . PHP_EOL;
        echo "    Client: " . ($o->client ? $o->client->name : 'N/A') . PHP_EOL;
        echo "    Address: " . ($o->client ? ($o->client->address ?: 'KOSONG') : 'N/A') . PHP_EOL;
    }
    echo PHP_EOL;
}

// Cek Projects
use App\Models\Project;
echo "PROJECTS:" . PHP_EOL;
echo "Total projects: " . Project::count() . PHP_EOL;

// Projects dimana client TIDAK punya alamat
$projectsWithoutClientAddress = Project::whereHas('client', function($q) {
    $q->where(function($q2) {
        $q2->whereNull('address')
           ->orWhere('address', '')
           ->orWhere('address', 'N/A');
    });
})->count();

echo "Projects dengan client TIDAK punya alamat: " . $projectsWithoutClientAddress . PHP_EOL;

// Projects dimana client PUNYA alamat
$projectsWithClientAddress = Project::whereHas('client', function($q) {
    $q->whereNotNull('address')
      ->where('address', '!=', '')
      ->where('address', '!=', 'N/A');
})->count();

echo "Projects dengan client PUNYA alamat: " . $projectsWithClientAddress . PHP_EOL . PHP_EOL;

// Sample projects tanpa alamat client
if ($projectsWithoutClientAddress > 0) {
    echo "Sample projects TANPA alamat client (butuh diupdate untuk PKS):" . PHP_EOL;
    $problemProjects = Project::whereHas('client', function($q) {
        $q->where(function($q2) {
            $q2->whereNull('address')
               ->orWhere('address', '')
               ->orWhere('address', 'N/A');
        });
    })->with('client:id,name,address')
      ->take(10)
      ->get(['id', 'project_name', 'client_id', 'status']);

    foreach($problemProjects as $p) {
        echo "  Project: {$p->project_name} (Status: {$p->status})" . PHP_EOL;
        echo "    Client: " . ($p->client ? $p->client->name : 'N/A') . PHP_EOL;
        echo "    Address: " . ($p->client ? ($p->client->address ?: 'KOSONG') : 'N/A') . PHP_EOL;
    }
}
