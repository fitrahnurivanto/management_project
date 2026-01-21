<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

echo "=== CEK PKS NUMBER: 088/PKS/CMU/XII/2025 ===" . PHP_EOL . PHP_EOL;

// Cek order dengan PKS tersebut
$order = Order::where('order_number', 'ORD-20251211-7205')->first();

if (!$order) {
    echo "Order ORD-20251211-7205 tidak ditemukan!" . PHP_EOL;
    exit;
}

echo "DETAIL ORDER:" . PHP_EOL;
echo "  Order Number: {$order->order_number}" . PHP_EOL;
echo "  PKS Number: {$order->pks_number}" . PHP_EOL;
echo "  Payment Status: {$order->payment_status}" . PHP_EOL;
echo "  Created At: " . $order->created_at->format('d M Y H:i') . PHP_EOL;
echo "  Confirmed At: " . ($order->confirmed_at ? $order->confirmed_at->format('d M Y H:i') : 'N/A') . PHP_EOL;
echo "  Order Date: " . ($order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : 'N/A') . PHP_EOL;
echo PHP_EOL;

// Cek PKS sebelum dan sesudah
echo "PKS SEBELUMNYA (087):" . PHP_EOL;
$before = Order::where('pks_number', 'LIKE', '087/PKS/CMU%')->first();
if ($before) {
    echo "  Order: {$before->order_number}" . PHP_EOL;
    echo "  PKS: {$before->pks_number}" . PHP_EOL;
    echo "  Confirmed: " . ($before->confirmed_at ? $before->confirmed_at->format('d M Y H:i') : 'N/A') . PHP_EOL;
} else {
    echo "  Tidak ditemukan" . PHP_EOL;
}
echo PHP_EOL;

echo "PKS SESUDAHNYA (089):" . PHP_EOL;
$after = Order::where('pks_number', 'LIKE', '089/PKS/CMU%')->first();
if ($after) {
    echo "  Order: {$after->order_number}" . PHP_EOL;
    echo "  PKS: {$after->pks_number}" . PHP_EOL;
    echo "  Confirmed: " . ($after->confirmed_at ? $after->confirmed_at->format('d M Y H:i') : 'N/A') . PHP_EOL;
} else {
    echo "  Tidak ditemukan" . PHP_EOL;
}
echo PHP_EOL;

// Penjelasan
echo "=== PENJELASAN ===" . PHP_EOL;
echo "PKS Number dibuat OTOMATIS saat order di-APPROVE/CONFIRM oleh admin." . PHP_EOL;
echo "Format: [NOMOR URUT]/PKS/CMU/[BULAN ROMAWI]/[TAHUN]" . PHP_EOL;
echo PHP_EOL;
echo "Nomor urut (088) increment otomatis berdasarkan PKS terakhir di tahun yang sama." . PHP_EOL;
echo "Jadi PKS 088 muncul setelah admin klik 'Approve Order' di halaman order management." . PHP_EOL;
