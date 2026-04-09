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
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->string('plant'); // B, H, I, T
            $table->string('operator_name');
            $table->string('group'); // A, B, C, D
            $table->integer('shift'); // 1, 2, 3
            $table->string('job_type'); // Scan, Strapping, Tempel Stiker, Susun Tire, Pressing
            $table->string('status'); // Team Leader, Operator, Driver Forklift
            $table->integer('ritase_result')->nullable(); // Only for Driver Forklift
            $table->date('date');
            $table->time('check_in');
            $table->time('check_out')->nullable();
            $table->integer('production_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};
