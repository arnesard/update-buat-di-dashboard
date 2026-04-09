<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah index untuk performa query dengan 100+ concurrent users.
     */
    public function up(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->index('date');
            $table->index('employee_id');
            $table->index('shift');
            $table->index(['date', 'employee_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('plant');
            $table->index('group');
            $table->index('name');
            $table->index(['plant', 'group']);
        });

        Schema::table('overtime_data', function (Blueprint $table) {
            $table->index('overtime_date');
            $table->index('status');
            $table->index('employee_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['shift']);
            $table->dropIndex(['date', 'employee_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['plant']);
            $table->dropIndex(['group']);
            $table->dropIndex(['name']);
            $table->dropIndex(['plant', 'group']);
        });

        Schema::table('overtime_data', function (Blueprint $table) {
            $table->dropIndex(['overtime_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['employee_name']);
        });
    }
};
