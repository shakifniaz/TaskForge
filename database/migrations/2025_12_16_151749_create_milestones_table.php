<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Long-term planning dates
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();

            // planned | in_progress | completed
            $table->string('status')->default('planned');

            // for timeline ordering if needed
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'target_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
