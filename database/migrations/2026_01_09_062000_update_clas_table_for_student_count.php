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
            // Tambah kolom price untuk harga per siswa
            $table->decimal('price', 10, 2)->after('slug');
            
            // Ubah amount menjadi integer untuk jumlah siswa
            $table->integer('amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clas', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
