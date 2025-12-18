<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('milestone_id')
                ->nullable()
                ->after('project_id')
                ->constrained('milestones')
                ->nullOnDelete();

            $table->index(['project_id', 'milestone_id']);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('milestone_id');
            $table->dropIndex(['project_id', 'milestone_id']);
        });
    }
};
