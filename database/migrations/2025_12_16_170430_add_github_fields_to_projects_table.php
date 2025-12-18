<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('github_repo')->nullable()->after('name');         // owner/repo
            $table->string('github_default_branch')->nullable()->after('github_repo');
            $table->text('github_token')->nullable()->after('github_default_branch'); // encrypted text
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['github_repo', 'github_default_branch', 'github_token']);
        });
    }
};
