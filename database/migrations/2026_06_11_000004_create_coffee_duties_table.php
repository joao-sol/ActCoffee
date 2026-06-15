<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coffee_duties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('original_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('duty_date')->unique();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['duty_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coffee_duties');
    }
};
