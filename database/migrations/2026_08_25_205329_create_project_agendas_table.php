<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // Info Agenda
            $table->string('title');
            $table->text('description')->nullable();
            
            // Kategori Beragam
            $table->enum('category', [
                'Meeting Online',
                'Meeting Offline',
                'Olahraga & Kesehatan',
                'Liburan & Outing',
                'Nonton & Hiburan',
                'Roadshow & Kunjungan',
                'Workshop & Pelatihan',
                'Lainnya'
            ])->default('Meeting Online');

            // Tanggal & Waktu
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Sifat Berulang (Recurring)
            $table->enum('recurrence', ['once', 'daily', 'weekly', 'monthly'])->default('once');
            $table->string('recurrence_days')->nullable(); // misal: "Monday, Thursday"

            // Lokasi / Platform Link
            $table->string('location_type')->default('online'); // 'online' atau 'offline'
            $table->string('meeting_url')->nullable(); // Link Google Meet / Zoom
            $table->string('location_address')->nullable(); // Alamat / Ruangan / Tempat

            // Peserta & Notulensi (JSON)
            $table->json('attendee_ids')->nullable(); // Array of user IDs yang diundang
            $table->text('agenda_notes')->nullable(); // Hasil notulensi / dokumentasi kegiatan

            $table->enum('status', ['Scheduled', 'Ongoing', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_agendas');
    }
};