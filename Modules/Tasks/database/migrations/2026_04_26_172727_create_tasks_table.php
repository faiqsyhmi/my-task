<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Content
            $table->string('title');
            $table->text('description')->nullable();

            // ADHD-Specific Metadata
            $table->smallInteger('energy_level')->default(3); // 1: Low, 5: High
            $table->integer('estimated_minutes')->nullable();

            // State & Timing
            $table->string('status')->default('todo'); // todo, doing, done
            $table->boolean('is_flagged')->default(false); // For "Important!" visual cues

            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexing for high-performance filtering in Livewire Datatables
            $table->index(['user_id', 'status', 'energy_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
