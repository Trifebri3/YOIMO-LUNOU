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
        Schema::create('project_asset_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('Branding & Desain'); // Branding & Desain, Konten & Media, Akses & Kredensial, Dokumen Legal, Lainnya
            $table->boolean('is_mandatory')->default(true);
            $table->enum('status', ['pending', 'submitted', 'approved'])->default('pending');

            // Client Submission Fields
            $table->string('submitted_by_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('external_url')->nullable();
            $table->text('client_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // Review / Feedback
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_feedback')->nullable();

            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_asset_requirements');
    }
};
