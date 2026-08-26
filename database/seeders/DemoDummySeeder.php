<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectAgenda;
use App\Models\ProjectDocument;
use App\Models\ProjectExpense;
use App\Models\ProjectRoadmap;
use App\Models\ProjectTask;
use App\Models\TaskProgressLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(?int $demoTrackId = null): void
    {
        // 0. Ensure users exist and fetch their IDs dynamically
        $manager = User::where('role', 'management')->first();
        if (! $manager) {
            $manager = User::create([
                'name' => 'Manager Eksekutif',
                'email' => 'management@gmail.com',
                'password' => bcrypt('password123'),
                'role' => 'management',
            ]);
        }
        $managerId = $manager->id;

        $employee = User::where('role', 'user')->first();
        if (! $employee) {
            $employee = User::create([
                'name' => 'Client User',
                'email' => 'user@gmail.com',
                'password' => bcrypt('password123'),
                'role' => 'user',
            ]);
        }
        $employeeId = $employee->id;

        // 1. Seed Company Profile
        $company = CompanyProfile::create([
            'manager_id' => $managerId,
            'demo_track_id' => $demoTrackId,
            'company_name' => 'DUMY',
            'slug' => 'dumy-'.Str::lower(Str::random(8)),
            'tagline' => 'Pusat Inovasi & Solusi Digital Terisolasi',
            'email' => 'hello@dumy.com',
            'phone' => '021-99998888',
            'address' => 'Kawasan Industri Kreatif Demo, Tower A, Lt. 3, Jakarta',
            'about' => 'DUMY adalah perusahaan teknologi fiktif yang digunakan untuk simulasi dan demonstrasi fitur-fitur Yoimo. Akun demo ini terisolasi dan akan terhapus otomatis setelah 3 jam.',
            'vision' => 'Menjadi sandbox simulasi manajemen tim terbaik dan terefektif.',
            'mission' => "1. Menyediakan lingkungan demonstrasi bebas resiko.\n2. Mensimulasikan alur kerja manajemen proyek secara komprehensif.\n3. Memberikan pengalaman integrasi wellbeing yang nyata.",
            'is_published' => true,
            'social_media' => [
                ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/dumy'],
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/dumy'],
            ],
            'dynamic_sections' => [
                [
                    'title' => 'Teknologi Sandbox',
                    'type' => 'text',
                    'content' => 'Setiap data pada perusahaan DUMY diisolasi khusus untuk sesi penjelajahan Anda. Silakan mencoba semua fitur tanpa khawatir.',
                ],
            ],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Seed Project
        $project = Project::create([
            'company_profile_id' => $company->id,
            'created_by' => $managerId,
            'demo_track_id' => $demoTrackId,
            'name' => 'Pengembangan Sistem ERP Terintegrasi',
            'slug' => 'erp-terintegrasi-'.Str::lower(Str::random(6)),
            'client_name' => 'Aero Corp',
            'category' => 'Software Development',
            'status' => 'Active',
            'priority' => 'High',
            'start_date' => Carbon::now()->subDays(10),
            'deadline' => Carbon::now()->addDays(20),
            'budget' => 250000000.00,
            'budget_spent' => 85000000.00,
            'is_financial_transparent' => true,
            'progress_percentage' => 35,
            'current_stage' => 'Development',
            'problem_statement' => 'Klien membutuhkan sistem ERP untuk mengintegrasikan pergudangan, manufaktur, dan keuangan yang selama ini berjalan secara terpisah.',
            'project_goals' => 'Meningkatkan efisiensi pelacakan inventaris hingga 40% dan mengotomatiskan laporan keuangan bulanan.',
            'expected_outputs' => 'Modul Inventaris Terintegrasi, Modul Laporan Keuangan Otomatis, Dashboard Analitik Real-time, dan Panduan Pengguna.',
            'services_rendered' => ['Consulting', 'UI/UX Design', 'Backend Engineering', 'Quality Assurance'],
            'tech_stacks' => ['Laravel 11', 'Vue.js 3', 'MySQL', 'Tailwind CSS'],
            'scope_included' => ['Sistem Pergudangan', 'Manajemen Vendor', 'Laporan Keuangan Neraca & Laba Rugi'],
            'scope_excluded' => ['Aplikasi Mobile Native (iOS & Android)', 'Integrasi Payment Gateway Global'],
            'milestones' => [
                ['title' => 'Figma Wireframe & UI Design', 'date' => Carbon::now()->subDays(5)->toDateString(), 'status' => 'Completed'],
                ['title' => 'Database & Backend API Core', 'date' => Carbon::now()->addDays(5)->toDateString(), 'status' => 'In Progress'],
                ['title' => 'Frontend Integration & Testing', 'date' => Carbon::now()->addDays(15)->toDateString(), 'status' => 'Pending'],
            ],
            'deliverables' => ['Dokumentasi API Core', 'Source Code Sandbox', 'Figma Prototype Link'],
            'team_matrix' => [
                ['user_id' => $managerId, 'role' => 'Project Manager', 'name' => 'Demo Manager'],
                ['user_id' => $employeeId, 'role' => 'Lead Developer', 'name' => 'Demo Employee'],
            ],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Seed Roadmaps
        $roadmap1 = ProjectRoadmap::create([
            'project_id' => $project->id,
            'title' => 'Fase 1: Perencanaan & Desain Basis Data',
            'description' => 'Tahap analisis kebutuhan, perancangan skema database, dan wireframing.',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->subDays(2),
            'status' => 'Completed',
            'progress_percentage' => 100,
            'objectives' => [
                ['target' => 'Skema Database ERD disetujui', 'is_achieved' => true],
                ['target' => 'Wireframe UI/UX disetujui klien', 'is_achieved' => true],
            ],
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        $roadmap2 = ProjectRoadmap::create([
            'project_id' => $project->id,
            'title' => 'Fase 2: Implementasi API & Modul Core',
            'description' => 'Pengembangan API back-end untuk modul inventarisasi dan sistem autentikasi.',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(8),
            'status' => 'In Progress',
            'progress_percentage' => 40,
            'objectives' => [
                ['target' => 'Modul Manajemen User & Autentikasi', 'is_achieved' => true],
                ['target' => 'Modul Pergudangan & Stok Barang', 'is_achieved' => false],
                ['target' => 'Integrasi API Client Portal', 'is_achieved' => false],
            ],
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now(),
        ]);

        // 4. Seed Tasks
        $task1 = ProjectTask::create([
            'project_id' => $project->id,
            'project_roadmap_id' => $roadmap1->id,
            'assigned_to' => $managerId,
            'created_by' => $managerId,
            'title' => 'Riset Kebutuhan Modul ERP & Database',
            'description' => 'Menganalisis kebutuhan data pergudangan Aero Corp dan menyusun rancangan skema database.',
            'priority' => 'High',
            'status' => 'Completed',
            'due_date' => Carbon::now()->subDays(5),
            'started_at' => Carbon::now()->subDays(9),
            'duration_minutes' => 240,
            'progress_percentage' => 100,
            'submission_notes' => 'Dokumen ERD dan kamus data telah selesai disusun.',
            'submitted_at' => Carbon::now()->subDays(5),
            'submission_timing_status' => 'On Time',
            'created_at' => Carbon::now()->subDays(9),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        $task2 = ProjectTask::create([
            'project_id' => $project->id,
            'project_roadmap_id' => $roadmap2->id,
            'assigned_to' => $employeeId,
            'created_by' => $managerId,
            'title' => 'Setup Boilerplate Laravel & DB Migrations',
            'description' => 'Menginisialisasi repository Laravel, konfigurasi database, dan pembuatan file migrations.',
            'priority' => 'Medium',
            'status' => 'Completed',
            'due_date' => Carbon::now()->subDays(2),
            'started_at' => Carbon::now()->subDays(3),
            'duration_minutes' => 180,
            'progress_percentage' => 100,
            'submission_notes' => 'Boilerplate project setup selesai dan database telah dimigrasikan.',
            'submitted_at' => Carbon::now()->subDays(2),
            'submission_timing_status' => 'On Time',
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        $task3 = ProjectTask::create([
            'project_id' => $project->id,
            'project_roadmap_id' => $roadmap2->id,
            'assigned_to' => $employeeId,
            'created_by' => $managerId,
            'title' => 'Implementasi API Modul Pergudangan',
            'description' => 'Membuat controller, model, dan API endpoint untuk manajemen stok barang masuk/keluar.',
            'priority' => 'High',
            'status' => 'In Progress',
            'due_date' => Carbon::now()->addDays(4),
            'started_at' => Carbon::now()->subDays(1),
            'duration_minutes' => 120,
            'progress_percentage' => 35,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now(),
        ]);

        // Add progress logs for Task 3
        TaskProgressLog::create([
            'project_task_id' => $task3->id,
            'user_id' => $employeeId,
            'progress_percentage' => 35,
            'notes' => 'Menyelesaikan skema tabel inventarisasi dan seeding data barang awal.',
            'obstacles' => 'Menyelaraskan relasi unit satuan barang dengan vendor.',
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(5),
        ]);

        $task4 = ProjectTask::create([
            'project_id' => $project->id,
            'project_roadmap_id' => $roadmap2->id,
            'assigned_to' => $managerId,
            'created_by' => $managerId,
            'title' => 'Penyusunan Kontrak Kerja & SOP Tim Developer',
            'description' => 'Menyusun dokumen kontrak kerja sama dan standard operating procedure untuk tim pengembang.',
            'priority' => 'Low',
            'status' => 'Todo',
            'due_date' => Carbon::now()->addDays(7),
            'progress_percentage' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 5. Seed Agendas
        ProjectAgenda::create([
            'project_id' => $project->id,
            'created_by' => $managerId,
            'title' => 'Rapat Kick-off & Penyelarasan Tim',
            'description' => 'Pertemuan tatap muka untuk membahas detail scope proyek ERP Aero Corp.',
            'category' => 'Meeting Offline',
            'start_date' => Carbon::now()->subDays(8),
            'end_date' => Carbon::now()->subDays(8),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'recurrence' => 'once',
            'location_type' => 'offline',
            'location_address' => 'Ruang Meeting Utama - Co-working Space Kancah',
            'attendee_ids' => [$managerId, $employeeId],
            'agenda_notes' => 'Klien menyetujui timeline dan metode koordinasi mingguan.',
            'status' => 'Completed',
            'created_at' => Carbon::now()->subDays(9),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        ProjectAgenda::create([
            'project_id' => $project->id,
            'created_by' => $managerId,
            'title' => 'Review Mingguan Sprint 1 (Progres API)',
            'description' => 'Rapat rutin mingguan via Zoom untuk memantau progres modul pergudangan.',
            'category' => 'Meeting Online',
            'start_date' => Carbon::now()->addDays(3),
            'end_date' => Carbon::now()->addDays(3),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'recurrence' => 'once',
            'location_type' => 'online',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'attendee_ids' => [$managerId, $employeeId],
            'status' => 'Scheduled',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 6. Seed Expenses
        ProjectExpense::create([
            'project_id' => $project->id,
            'created_by' => $managerId,
            'title' => 'Pembelian Cloud VPS (AWS Lightsail)',
            'category' => 'Infrastruktur & Server',
            'amount' => 1500000.00,
            'expense_date' => Carbon::now()->subDays(7),
            'notes' => 'Pembayaran server hosting dev staging untuk 1 tahun ke depan.',
            'status' => 'Approved',
            'created_at' => Carbon::now()->subDays(7),
            'updated_at' => Carbon::now()->subDays(7),
        ]);

        ProjectExpense::create([
            'project_id' => $project->id,
            'created_by' => $managerId,
            'title' => 'Sewa Co-working Space & Makan Tim',
            'category' => 'Operasional & Konsumsi',
            'amount' => 750000.00,
            'expense_date' => Carbon::now()->subDays(8),
            'notes' => 'Rapat kick-off tatap muka di co-working space.',
            'status' => 'Approved',
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        // 7. Seed Documents
        ProjectDocument::create([
            'project_id' => $project->id,
            'created_by' => $managerId,
            'title' => 'SOP & Panduan Standar Kode Git',
            'category' => 'SOP & Panduan Kerja',
            'is_mandatory' => true,
            'doc_type' => 'article',
            'content' => "## Panduan Penulisan Commit Git\n1. Gunakan format conventional commits: `feat:`, `fix:`, `docs:`, `refactor:`.\n2. Lakukan pull request sebelum merge ke branch `main`.\n3. Pastikan CI/CD pipeline berhasil sebelum merge.",
            'readers_log' => [],
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        ProjectDocument::create([
            'project_id' => $project->id,
            'created_by' => $managerId,
            'title' => 'Dokumen Spesifikasi Teknis ERP',
            'category' => 'Spesifikasi Teknis & API',
            'is_mandatory' => false,
            'doc_type' => 'link',
            'external_url' => 'https://notion.so/dummy-erp-spec',
            'readers_log' => [],
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        // 8. Seed Activity Logs
        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => $managerId,
            'user_name' => 'Demo Manager',
            'user_role' => 'management',
            'action' => 'CREATE',
            'module' => 'Project',
            'description' => 'Membuat proyek baru: Pengembangan Sistem ERP Terintegrasi.',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ]);

        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => $managerId,
            'user_name' => 'Demo Manager',
            'user_role' => 'management',
            'action' => 'CREATE',
            'module' => 'Roadmap',
            'description' => 'Menambahkan fase linimasa baru pada proyek.',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ]);
    }
}
