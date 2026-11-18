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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('location_id')
                ->nullable()
                ->constrained();
            $table->foreignId('area_id')
                ->nullable()
                ->constrained();
            $table->unsignedBigInteger('current_responsible_employee_id')->nullable();

            $table->string('code')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('status')->default('available');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
