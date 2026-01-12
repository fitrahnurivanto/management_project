<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== EMPLOYEE ACCOUNTS ===\n\n";

$employees = App\Models\User::where('role', 'employee')->get(['name', 'email']);

if ($employees->isEmpty()) {
    echo "No employees found. Creating test employee...\n\n";
    
    $employee = App\Models\User::create([
        'name' => 'Test Employee',
        'email' => 'employee@test.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Employee created:\n";
    echo "Name: {$employee->name}\n";
    echo "Email: {$employee->email}\n";
    echo "Password: password\n\n";
} else {
    foreach ($employees as $emp) {
        echo "Name: {$emp->name}\n";
        echo "Email: {$emp->email}\n";
        echo "Password: password (default)\n";
        echo "---\n";
    }
}

echo "\n=== ADMIN ACCOUNTS ===\n\n";

$admins = App\Models\User::where('role', 'admin')->orWhere('role', 'super_admin')->get(['name', 'email', 'role']);

foreach ($admins as $admin) {
    echo "Name: {$admin->name}\n";
    echo "Email: {$admin->email}\n";
    echo "Role: {$admin->role}\n";
    echo "Password: password (default)\n";
    echo "---\n";
}

echo "\n✅ Done!\n";
