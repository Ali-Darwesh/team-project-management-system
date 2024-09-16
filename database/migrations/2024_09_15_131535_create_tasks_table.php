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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->foreignId('user_id')->constrained();
            $table->date('due_date');
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->string('notes')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the `deleted_at` column
        });
    }
};
