<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coffee_duties', function (Blueprint $table): void {
            $table->foreignId('covered_employee_id')->nullable()->after('original_employee_id')->constrained('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coffee_duties', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('covered_employee_id');
        });
    }
};
