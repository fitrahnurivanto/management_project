<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    echo "=== Merging Duplicate Clients ===\n\n";
    
    // Client 63 is the main one (has user_id=21)
    $mainClient = Client::find(63);
    echo "Main Client 63: {$mainClient->name} ({$mainClient->email})\n";
    echo "User ID: {$mainClient->user_id}\n\n";
    
    // Merge Client 65 (ivan)
    $client65 = Client::find(65);
    if ($client65) {
        echo "Merging Client 65 (ivan)...\n";
        $orders65 = Order::where('client_id', 65)->get();
        echo "Found {$orders65->count()} orders\n";
        
        foreach ($orders65 as $order) {
            $order->client_id = 63;
            $order->save();
            echo "  - Updated {$order->order_number}\n";
        }
        
        $projects65 = Project::where('client_id', 65)->get();
        echo "Found {$projects65->count()} projects\n";
        foreach ($projects65 as $project) {
            $project->client_id = 63;
            $project->save();
            echo "  - Updated {$project->project_code}\n";
        }
        
        $client65->delete();
        echo "Client 65 deleted\n\n";
    }
    
    // Merge Client 70 (fitrah nur ivanto)
    $client70 = Client::find(70);
    if ($client70) {
        echo "Merging Client 70 (fitrah nur ivanto)...\n";
        $orders70 = Order::where('client_id', 70)->get();
        echo "Found {$orders70->count()} orders\n";
        
        foreach ($orders70 as $order) {
            $order->client_id = 63;
            $order->save();
            echo "  - Updated {$order->order_number}\n";
        }
        
        $projects70 = Project::where('client_id', 70)->get();
        echo "Found {$projects70->count()} projects\n";
        foreach ($projects70 as $project) {
            $project->client_id = 63;
            $project->save();
            echo "  - Updated {$project->project_code}\n";
        }
        
        $client70->delete();
        echo "Client 70 deleted\n\n";
    }
    
    // Show final result
    echo "=== Final Result ===\n";
    $finalOrders = Order::where('client_id', 63)->get();
    echo "Client 63 now has {$finalOrders->count()} orders:\n";
    foreach ($finalOrders as $order) {
        echo "  - {$order->order_number}\n";
    }
    
    DB::commit();
    echo "\n✅ Merge completed successfully!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
