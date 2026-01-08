<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ServiceCategory;

echo "Checking and fixing Academy service category division...\n\n";

// Find Academy category
$academyCategory = ServiceCategory::where('name', 'Academy')->first();

if ($academyCategory) {
    echo "Found Academy category (ID: {$academyCategory->id})\n";
    echo "Current division: " . ($academyCategory->division ?? 'NULL') . "\n";
    
    if ($academyCategory->division !== 'academy') {
        $academyCategory->division = 'academy';
        $academyCategory->save();
        echo "✓ Updated Academy category division to 'academy'\n";
    } else {
        echo "✓ Academy category already has correct division\n";
    }
} else {
    echo "✗ Academy category not found!\n";
    echo "Creating Academy category...\n";
    
    $academyCategory = ServiceCategory::create([
        'name' => 'Academy',
        'slug' => 'academy',
        'description' => 'Program pelatihan dan sertifikasi',
        'division' => 'academy',
        'icon' => 'graduation-cap',
        'is_active' => true,
        'display_order' => 4,
    ]);
    
    echo "✓ Created Academy category with division 'academy'\n";
}

echo "\nDone! Academy orders should now appear in admin academy dashboard.\n";
