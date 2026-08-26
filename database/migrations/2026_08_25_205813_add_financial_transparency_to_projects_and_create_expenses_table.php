<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Toggle Transparansi Keuangan di Tabel Projects
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_financial_transparent')->default(true)->after('budget_spent');
        });

        // 2. Buat Tabel Project Expenses (Laporan Belanja)
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // User Finance / Pengunggah
            
            $table->string('title'); // Contoh: "Pembelian Domain & Server AWS 1 Tahun"
            $table->enum('category', [
                'Infrastruktur & Server',
                'Lisensi & Software',
                'Operasional & Konsumsi',
                'Peralatan & Hardware',
                'Honorarium & Jasa',
                'Marketing & Promosi',
                'Lainnya'
            ])->default('Operasional & Konsumsi');

            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('receipt_file')->nullable(); // Foto Struk / Nota PDF / Gambar Kwitansi
            $table->text('notes')->nullable();
            $table->enum('status', ['Approved', 'Pending', 'Rejected'])->default('Approved');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_financial_transparent');
        });
    }
};