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
        Schema::create('clas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->integer('meet');
            $table->integer('duration');
            $table->enum('method', ['online', 'offline']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('trainer');
            $table->decimal('income', 10, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['pending','approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clas');
    }
};
