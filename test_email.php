<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email dari Management System', function($message) {
        $message->to('fitrahnurivanto@gmail.com')
                ->subject('Test Email Notifikasi');
    });
    
    echo "✅ Email berhasil dikirim ke fitrahnurivanto@gmail.com!\n";
    echo "Silakan cek inbox email Anda.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
