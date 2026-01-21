<?php

use Illuminate\Support\Facades\DB;

// Check January 2026 orders
echo "=== Januari 2026 Orders ===\n";

$orders = DB::table('orders')
    ->where('payment_status', 'paid')
    ->whereRaw('COALESCE(order_date, confirmed_at) BETWEEN ? AND ?', ['2026-01-01', '2026-01-31'])
    ->get(['id', 'order_number', 'paid_amount', 'total_amount', 'order_date', 'confirmed_at']);

foreach ($orders as $order) {
    echo "Order: {$order->order_number}\n";
    echo "  Paid Amount: Rp " . number_format($order->paid_amount, 0) . "\n";
    echo "  Total: Rp " . number_format($order->total_amount, 0) . "\n";
    echo "  Date: " . ($order->order_date ?? $order->confirmed_at) . "\n";
    
    // Check division
    $division = DB::table('order_items')
        ->join('services', 'order_items.service_id', '=', 'services.id')
        ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
        ->where('order_items.order_id', $order->id)
        ->value('service_categories.division');
    
    echo "  Division: " . ($division ?? 'N/A') . "\n\n";
}

// Check weekly breakdown
echo "\n=== Weekly Breakdown ===\n";
for ($week = 1; $week <= 4; $week++) {
    $weekStart = \Carbon\Carbon::create(2026, 1, 1)->addWeeks($week - 1);
    $weekEnd = $weekStart->copy()->addWeeks(1)->subDay();
    
    $revenue = DB::table('orders')
        ->where('payment_status', 'paid')
        ->whereRaw('COALESCE(order_date, confirmed_at) BETWEEN ? AND ?', [$weekStart, $weekEnd])
        ->whereHas('items.service.category', function($q) {
            $q->where('division', 'agency');
        })
        ->sum('paid_amount');
    
    echo "Week {$week} ({$weekStart->format('d M')} - {$weekEnd->format('d M')}): Rp " . number_format($revenue, 0) . "\n";
}

echo "\nDone!\n";
