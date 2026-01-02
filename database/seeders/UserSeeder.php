<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);
        
        echo "✅ Admin created: {$admin->email} / password\n";

        // Create Employee Users
        $employee1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '081234567891',
        ]);
        
        $employee2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '081234567892',
        ]);
        
        $employee3 = User::create([
            'name' => 'Ahmad Hidayat',
            'email' => 'ahmad@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone' => '081234567893',
        ]);

        echo "✅ Employees created:\n";
        echo "   - {$employee1->email} / password (Developer)\n";
        echo "   - {$employee2->email} / password (Content Creator)\n";
        echo "   - {$employee3->email} / password (Marketing)\n";

        // Create Client Users
        $client1 = User::create([
            'name' => 'PT Maju Jaya',
            'email' => 'client1@test.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '081234567894',
        ]);

        Client::create([
            'user_id' => $client1->id,
            'company_name' => 'PT Maju Jaya',
            'company_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'business_type' => 'E-commerce',
            'npwp' => '01.234.567.8-901.000',
            'contact_person' => 'John Doe',
            'contact_phone' => '081234567894',
        ]);

        $client2 = User::create([
            'name' => 'CV Berkah Sejahtera',
            'email' => 'client2@test.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '081234567895',
        ]);

        Client::create([
            'user_id' => $client2->id,
            'company_name' => 'CV Berkah Sejahtera',
            'company_address' => 'Jl. Gatot Subroto No. 456, Jakarta Selatan',
            'business_type' => 'Fashion',
            'npwp' => '02.345.678.9-012.000',
            'contact_person' => 'Jane Smith',
            'contact_phone' => '081234567895',
        ]);

        echo "✅ Clients created:\n";
        echo "   - {$client1->email} / password (PT Maju Jaya)\n";
        echo "   - {$client2->email} / password (CV Berkah Sejahtera)\n";

        echo "\n";
        echo "==================================================\n";
        echo "  USERS CREATED SUCCESSFULLY!\n";
        echo "==================================================\n";
        echo "  All passwords: password\n";
        echo "==================================================\n";
    }
}
