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
        Schema::create('equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('client_id')
                ->constrained();
            $table->unsignedBigInteger('employee_id');
            $table->foreignId('location_id')
                ->nullable()
                ->constrained();
            $table->foreignId('area_id')
                ->nullable()
                ->constrained();

            $table->string('assignment_type');
            $table->timestamp('assigned_at');
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['equipment_id', 'assignment_type']);
            $table->index(['client_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_assignments');
    }
};
