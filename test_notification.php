<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Notifications\ProjectCreatedNotification;
use App\Notifications\ProjectStatusChangedNotification;

echo "=== TEST NOTIFICATION SYSTEM ===\n\n";

// Pilihan menu
echo "Pilih jenis notifikasi yang ingin di-test:\n";
echo "1. Notifikasi Project Baru ke Client\n";
echo "2. Notifikasi Perubahan Status Project ke Client\n";
echo "3. Notifikasi Project Baru ke Team Member\n";
echo "\nMasukkan pilihan (1-3): ";

$choice = trim(fgets(STDIN));

try {
    switch ($choice) {
        case '1':
            // Test: Project Created Notification ke Client
            echo "\n--- Test: Notifikasi Project Baru ke Client ---\n";
            
            // Ambil project terakhir
            $project = Project::with('client')->latest()->first();
            
            if (!$project) {
                echo "❌ Tidak ada project di database.\n";
                exit;
            }
            
            if (!$project->client) {
                echo "❌ Project tidak memiliki client.\n";
                exit;
            }
            
            echo "Project: {$project->project_name}\n";
            echo "Client: {$project->client->name} ({$project->client->email})\n";
            echo "\nMengirim notifikasi...\n";
            
            // Kirim notifikasi ke client
            $project->client->notify(new ProjectCreatedNotification($project));
            
            echo "✅ Notifikasi berhasil dikirim ke {$project->client->email}!\n";
            echo "📧 Silakan cek inbox email.\n";
            break;
            
        case '2':
            // Test: Project Status Changed Notification
            echo "\n--- Test: Notifikasi Perubahan Status Project ---\n";
            
            $project = Project::with('client')->latest()->first();
            
            if (!$project || !$project->client) {
                echo "❌ Project atau client tidak ditemukan.\n";
                exit;
            }
            
            $oldStatus = $project->status;
            $newStatus = $oldStatus === 'pending' ? 'in_progress' : 'completed';
            
            echo "Project: {$project->project_name}\n";
            echo "Client: {$project->client->name} ({$project->client->email})\n";
            echo "Status: {$oldStatus} → {$newStatus}\n";
            echo "\nMengirim notifikasi...\n";
            
            // Kirim notifikasi
            $project->client->notify(new ProjectStatusChangedNotification($project, $oldStatus, $newStatus));
            
            echo "✅ Notifikasi berhasil dikirim ke {$project->client->email}!\n";
            echo "📧 Silakan cek inbox email.\n";
            break;
            
        case '3':
            // Test: Project Created ke Team Member
            echo "\n--- Test: Notifikasi Project ke Team Member ---\n";
            
            $project = Project::with('client')->latest()->first();
            
            if (!$project) {
                echo "❌ Tidak ada project di database.\n";
                exit;
            }
            
            // Ambil employee untuk test
            $employee = User::where('role', 'employee')->first();
            
            if (!$employee) {
                echo "❌ Tidak ada employee di database.\n";
                exit;
            }
            
            echo "Project: {$project->project_name}\n";
            echo "Employee: {$employee->name} ({$employee->email})\n";
            echo "\nMengirim notifikasi...\n";
            
            // Kirim notifikasi
            $employee->notify(new ProjectCreatedNotification($project));
            
            echo "✅ Notifikasi berhasil dikirim ke {$employee->email}!\n";
            echo "📧 Silakan cek inbox email.\n";
            break;
            
        default:
            echo "❌ Pilihan tidak valid.\n";
            exit;
    }
    
    echo "\n💡 Tips: Notifikasi ini akan otomatis terkirim saat:\n";
    echo "   - Admin membuat project baru\n";
    echo "   - Admin mengubah status project\n";
    echo "   - Admin assign team member ke project\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetail Error:\n";
    echo $e->getTraceAsString() . "\n";
}
