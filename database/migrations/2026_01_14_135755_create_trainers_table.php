<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Nama trainer
            $table->string('email')->unique();   // Login / kontak
            $table->string('phone')->nullable(); // WhatsApp
            $table->text('bio')->nullable();     // Profil singkat
            $table->string('expertise');         // Keahlian (Laravel, UI/UX, dll)
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
