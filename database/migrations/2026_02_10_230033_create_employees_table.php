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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_id')->unique();
            $table->string('plant'); // B, H, I, T
            $table->string('group'); // A, B, C, D
            $table->string('default_status'); // Team Leader, Operator, Driver Forklift
            $table->string('primary_job_type'); // Scan, Strapping, Tempel Stiker, Susun Tire, Pressing
            $table->boolean('is_active')->default(true);
            $table->date('hire_date');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
