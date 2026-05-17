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
        Schema::create('routines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Content
            $table->string('title');
            $table->text('description')->nullable();

            // ADHD-Specific Metadata
            $table->smallInteger('energy_level')->default(3); // 1: Low, 5: High
            $table->integer('estimated_minutes')->nullable();

            // State & Timing
            $table->string('frequency')->default('daily'); // daily, weekly
            $table->timestamp('last_completed_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexing for performance
            $table->index(['user_id', 'is_active', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
};
