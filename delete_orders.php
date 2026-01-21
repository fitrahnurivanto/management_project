<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

$orderNumbers = [
    'ORD-20260108-0006',
    'ORD-20260108-0005',
    'ORD-20260108-0001',
    'ORD-20251230-0001',
    'ORD-20260107-0001',
    'ORD-20260107-0002'
];

echo "=== CEK ORDERS YANG MAU DIHAPUS ===" . PHP_EOL . PHP_EOL;

$orders = Order::whereIn('order_number', $orderNumbers)
    ->with('items', 'project')
    ->get();

if ($orders->isEmpty()) {
    echo "Tidak ada orders ditemukan!" . PHP_EOL;
    exit;
}

foreach ($orders as $order) {
    echo "Order: {$order->order_number}" . PHP_EOL;
    echo "  ID: {$order->id}" . PHP_EOL;
    echo "  PKS: " . ($order->pks_number ?: 'N/A') . PHP_EOL;
    echo "  Status: {$order->payment_status}" . PHP_EOL;
    echo "  Total: Rp " . number_format($order->total_amount, 0, ',', '.') . PHP_EOL;
    echo "  Items: " . $order->items->count() . PHP_EOL;
    echo "  Has Project: " . ($order->project ? 'YES - ' . $order->project->project_name : 'NO') . PHP_EOL;
    echo PHP_EOL;
}

echo "=== KONFIRMASI HAPUS ===" . PHP_EOL;
echo "Ketik 'yes' untuk hapus semua orders di atas: ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) === 'yes') {
    echo PHP_EOL . "Menghapus orders..." . PHP_EOL;
    
    foreach ($orders as $order) {
        // Hapus project jika ada
        if ($order->project) {
            echo "  - Hapus project: {$order->project->project_name}" . PHP_EOL;
            $order->project->delete();
        }
        
        // Hapus order items
        echo "  - Hapus {$order->items->count()} order items" . PHP_EOL;
        $order->items()->delete();
        
        // Hapus order
        echo "  - Hapus order: {$order->order_number}" . PHP_EOL;
        $order->delete();
        
        echo "    ✓ Berhasil!" . PHP_EOL . PHP_EOL;
    }
    
    echo "=== SELESAI ===" . PHP_EOL;
    echo "Total " . $orders->count() . " orders telah dihapus!" . PHP_EOL;
} else {
    echo "Dibatalkan." . PHP_EOL;
}
