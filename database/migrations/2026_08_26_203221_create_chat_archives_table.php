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
        Schema::create('chat_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('archived_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('archived_project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->timestamps();

            // Ensure unique archiving per user
            $table->unique(['user_id', 'archived_user_id']);
            $table->unique(['user_id', 'archived_project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_archives');
    }
};
