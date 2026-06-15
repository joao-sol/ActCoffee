<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->unsignedInteger('queue_position')->unique();
            $table->date('hired_at')->nullable();
            $table->date('dismissed_at')->nullable();
            $table->timestamps();

            $table->index(['active', 'queue_position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
