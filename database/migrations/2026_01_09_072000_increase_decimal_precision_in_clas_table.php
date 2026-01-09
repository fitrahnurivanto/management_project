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
        Schema::table('clas', function (Blueprint $table) {
            // Ubah dari DECIMAL(10, 2) menjadi DECIMAL(15, 2)
            // Maksimal nilai: 9,999,999,999,999.99 (hampir 10 triliun)
            $table->decimal('price', 15, 2)->change();
            $table->decimal('cost', 15, 2)->change();
            $table->decimal('income', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clas', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('cost', 10, 2)->change();
            $table->decimal('income', 10, 2)->change();
        });
    }
};
