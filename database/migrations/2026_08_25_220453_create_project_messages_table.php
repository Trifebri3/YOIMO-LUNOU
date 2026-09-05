<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            // Nullable recipient_id (for personal chat)
            $table->foreignId('recipient_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Nullable project_id (for project group chat)
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();

            $table->text('message')->nullable();
            $table->string('attachment_file')->nullable();
            $table->string('attachment_name')->nullable();
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_messages');
    }
};
