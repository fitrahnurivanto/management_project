<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Resetting passwords for all employees...\n\n";

$employees = App\Models\User::where('role', 'employee')->get();

foreach ($employees as $employee) {
    $employee->password = bcrypt('password123');
    $employee->email_verified_at = now();
    $employee->save();
    
    echo "✅ {$employee->name}\n";
    echo "   Email: {$employee->email}\n";
    echo "   Password: password123\n\n";
}

echo "Resetting passwords for all admins...\n\n";

$admins = App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();

foreach ($admins as $admin) {
    $admin->password = bcrypt('password123');
    $admin->email_verified_at = now();
    $admin->save();
    
    echo "✅ {$admin->name}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Password: password123\n\n";
}

echo "✅ All passwords reset to: password123\n";
