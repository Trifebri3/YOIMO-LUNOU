<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            $table->enum('category', [
                'SOP & Panduan Kerja',
                'Kontrak & Legalitas',
                'Spesifikasi Teknis & API',
                'Desain & Brand Asset',
                'Laporan & Riset',
                'Template & Format',
                'Lainnya'
            ])->default('SOP & Panduan Kerja');

            // Sifat Panduan (Wajib atau Opsional)
            $table->boolean('is_mandatory')->default(false);

            // Jenis Format Dokumen
            $table->enum('doc_type', ['file', 'link', 'article'])->default('file');
            
            $table->string('file_path')->nullable(); // File upload (PDF/Doc/Zip)
            $table->string('file_name_original')->nullable();
            $table->string('file_size')->nullable();
            $table->string('external_url')->nullable(); // Tautan Notion/Figma/Drive
            $table->longText('content')->nullable(); // Teks panduan langsung (Rich Text)

            // Tracking Pembaca (JSON Array: [{"user_id": 1, "read_at": "2026-08-26 10:00:00"}])
            $table->json('readers_log')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};