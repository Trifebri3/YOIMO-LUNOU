<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectAiChat;
use App\Models\ProjectExpense;
use App\Models\ProjectTask;
use App\Models\TaskProgressLog;
use App\Services\AIService;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function show(Request $request, Project $project): View
    {
        $userId = Auth::id();

        $project->load([
            'company',
            'creator',
            'roadmaps',
            'agendas.creator',
            'repositoryDocuments.creator',
            'expenses.uploader',
            'tasks' => function ($query) {
                $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
            },
            'tasks.assignee',
            'tasks.roadmap',
            'tasks.progressLogs.user',
            'activityLogs.user',
        ]);

        $myTasks = $project->tasks->where('assigned_to', $userId);
        $openTasks = $project->tasks->whereNull('assigned_to')->where('status', '!=', 'Completed');
        $allTasks = $project->tasks;

        $activeTab = $request->query('tab', 'overview');
        $myPendingCount = $myTasks->whereIn('status', ['Todo', 'In Progress'])->count();
        $totalExpensesApproved = $project->expenses->where('status', 'Approved')->sum('amount');
        $remainingBudget = $project->budget - $totalExpensesApproved;

        // Dynamic LUNOU AI Project Health Insight
        $projectAiInsight = Cache::remember('project_ai_insight_'.$project->id, 1800, function () use ($project, $allTasks, $totalExpensesApproved) {
            try {
                $aiService = app(AIService::class);
                $tasksSummary = '';
                foreach ($allTasks as $t) {
                    $tasksSummary .= "- {$t->title} ({$t->status}, {$t->progress_percentage}%)\n";
                }

                $prompt = "Kamu adalah LUNOU, asisten AI proyek Yoimo. Berikan analisis kesehatan/keadaan proyek singkat dan saran praktis untuk tim.\n\n"
                    ."Detail Proyek:\n"
                    ."- Nama Proyek: {$project->name}\n"
                    ."- Kategori: {$project->category}\n"
                    ."- Tahapan: {$project->current_stage}\n"
                    ."- Progres Keseluruhan: {$project->progress_percentage}%\n"
                    .'- Keuangan: Budget Rp '.number_format($project->budget, 0, ',', '.').', Terpakai Rp '.number_format($totalExpensesApproved, 0, ',', '.')."\n"
                    ."- Daftar Tugas:\n{$tasksSummary}\n\n"
                    ."Berikan:\n"
                    ."1. Analisis Singkat (2-3 kalimat): Evaluasi status saat ini, beban kerja, dan kesehatan progres secara empati.\n"
                    ."2. Rekomendasi Penting (3 poin): Tips praktis/langkah konkret selanjutnya yang perlu diambil tim agar sukses.\n"
                    .'Gunakan Bahasa Indonesia yang ramah, sopan, dan suportif.';

                $res = $aiService->chat([
                    'system' => 'Asisten analisis kesehatan proyek Yoimo.',
                    'message' => $prompt,
                    'temperature' => 0.7,
                ]);

                return $res->content;
            } catch (\Exception $e) {
                return 'LUNOU belum berhasil menganalisis proyek ini. Namun, pastikan tim Anda terus memperbarui progres tugas dan menjaga komunikasi yang sehat ya!';
            }
        });

        // Fetch AI History logs
        $projectAiChats = ProjectAiChat::where('project_id', $project->id)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $aiProgressLogs = TaskProgressLog::whereHas('task', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })
            ->with(['task', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.projects.show', compact(
            'project',
            'myTasks',
            'openTasks',
            'allTasks',
            'activeTab',
            'myPendingCount',
            'totalExpensesApproved',
            'remainingBudget',
            'projectAiInsight',
            'projectAiChats',
            'aiProgressLogs'
        ));
    }

    /**
     * 1. Mulai Kerjakan Tugas (Timer Mulai Berjalan)
     */
    public function startTask(Project $project, ProjectTask $task): RedirectResponse
    {
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Hanya penanggung jawab yang dapat memulai tugas.');
        }

        $task->update([
            'status' => 'In Progress',
            'started_at' => $task->started_at ?? now(),
        ]);

        ProjectActivityLog::record(
            $project->id,
            'Task',
            'UPDATE',
            "Memulai pengerjaan tugas: '{$task->title}' (Timer berjalan)"
        );

        return back()->with('success', 'Timer pengerjaan tugas telah dimulai.');
    }

    /**
     * 2. Log Progres Bertahap (misal: 25%, 50%, 75%)
     */
    public function logPartialProgress(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'progress_percentage' => ['required', 'integer', 'min:1', 'max:99'],
            'notes' => ['required', 'string'],
            'obstacles' => ['nullable', 'string'],
            'attachment_url' => ['nullable', 'url'],
            'attachment_file' => ['nullable', 'file', 'max:15360'],
        ]);

        $filePath = null;
        if ($request->hasFile('attachment_file')) {
            $filePath = $request->file('attachment_file')->store('task_progress', 'public');
        }

        TaskProgressLog::create([
            'project_task_id' => $task->id,
            'user_id' => Auth::id(),
            'progress_percentage' => $request->progress_percentage,
            'notes' => $request->notes,
            'obstacles' => $request->obstacles,
            'attachment_url' => $request->attachment_url,
            'attachment_file' => $filePath,
        ]);

        // Hitung akumulasi durasi
        $duration = $task->duration_minutes;
        if ($task->started_at) {
            $duration += abs(now()->diffInMinutes($task->started_at));
        }

        $task->update([
            'progress_percentage' => $request->progress_percentage,
            'duration_minutes' => $duration,
            'status' => 'In Progress',
        ]);

        ProjectActivityLog::record(
            $project->id,
            'Task',
            'UPDATE',
            "Melaporkan progres bertahap {$request->progress_percentage}% untuk tugas: '{$task->title}'"
        );

        return back()->with('success', "Progres {$request->progress_percentage}% berhasil disimpan ke riwayat tugas.");
    }

    /**
     * 3. Pengajuan Selesai 100% (Final Submission dengan Evaluasi & Deteksi Ketepatan Waktu)
     */
    public function submitFinal(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        if ($task->assigned_to !== null && Auth::id() !== $task->assigned_to) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'submission_notes' => ['required', 'string'],
            'obstacles_faced' => ['nullable', 'string'],
            'self_evaluation' => ['nullable', 'string'],
            'submission_link' => ['nullable', 'url'],
            'submission_file' => ['nullable', 'file', 'max:15360'],
        ]);

        // Hitung total durasi menit
        $duration = $task->duration_minutes;
        if ($task->started_at) {
            $duration += abs(now()->diffInMinutes($task->started_at));
        }

        // Cek Ketepatan Waktu (On Time vs Overdue)
        $timingStatus = 'On Time';
        if ($task->due_date && now()->startOfDay()->gt($task->due_date->startOfDay())) {
            $timingStatus = 'Overdue';
        }

        $updateData = [
            'submission_notes' => $request->submission_notes,
            'obstacles_faced' => $request->obstacles_faced,
            'self_evaluation' => $request->self_evaluation,
            'submission_link' => $request->submission_link,
            'progress_percentage' => 100,
            'duration_minutes' => $duration,
            'submitted_at' => now(),
            'submission_timing_status' => $timingStatus,
            'status' => 'Review',
            'revision_notes' => null, // Reset catatan revisi lama jika ada
        ];

        if ($task->assigned_to === null) {
            $updateData['assigned_to'] = Auth::id();
        }

        if ($request->hasFile('submission_file')) {
            if ($task->submission_file && Storage::disk('public')->exists($task->submission_file)) {
                Storage::disk('public')->delete($task->submission_file);
            }
            $updateData['submission_file'] = $request->file('submission_file')->store('task_submissions', 'public');
        }

        $task->update($updateData);

        // Catat juga ke log progres
        TaskProgressLog::create([
            'project_task_id' => $task->id,
            'user_id' => Auth::id(),
            'progress_percentage' => 100,
            'notes' => 'Pengajuan Penyelesaian Tugas (100%): '.strip_tags($request->submission_notes),
            'obstacles' => $request->obstacles_faced,
            'attachment_url' => $request->submission_link,
            'attachment_file' => $updateData['submission_file'] ?? null,
        ]);

        ProjectActivityLog::record(
            $project->id,
            'Task',
            'SUBMIT',
            "Mengajukan penyelesaian tugas 100% ({$timingStatus}): '{$task->title}'"
        );

        $isManagerOrCreator = ($project->created_by === Auth::id()) || ($project->company && $project->company->manager_id === Auth::id());
        if ($isManagerOrCreator) {
            $task->update(['status' => 'Completed']);
            GamificationService::awardTaskCompletion($task, Auth::id());
            $successMsg = "Tugas berhasil diselesaikan 100% ({$timingStatus})! Poin XP dan tracing telah ditambahkan ke profil Anda.";
        } else {
            GamificationService::awardTaskSubmission($task, Auth::id());
            $successMsg = "Tugas berhasil diajukan 100% ({$timingStatus}). Anda mendapatkan +25 XP dan tugas menunggu verifikasi Management.";
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Klaim Tugas Terbuka
     */
    public function claimTask(Project $project, ProjectTask $task): RedirectResponse
    {
        if ($task->assigned_to !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh user lain');
        }

        $task->update([
            'assigned_to' => Auth::id(),
            'status' => 'In Progress',
        ]);

        ProjectActivityLog::record(
            $project->id,
            'Task',
            'CLAIM',
            "Mengambil/Mengklaim penugasan tugas terbuka: '{$task->title}'"
        );

        return back()->with('success', 'Anda berhasil mengambil tugas ini. Tugas sekarang menjadi tanggung jawab Anda.');
    }

    /**
     * Simpan pengeluaran baru (Hanya untuk Finance)
     */
    public function storeExpense(Request $request, Project $project): RedirectResponse
    {
        if (Auth::user()->role !== 'finance') {
            abort(403, 'Hanya Staff Keuangan yang dapat melakukan aksi ini.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date'],
            'receipt_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'Approved';

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('expense_receipts', 'public');
        }

        ProjectExpense::create($validated);

        // Update nominal budget_spent di tabel projects secara otomatis
        $totalApproved = $project->expenses()->where('status', 'Approved')->sum('amount');
        $project->update(['budget_spent' => $totalApproved]);

        // Catat Audit Log
        ProjectActivityLog::record(
            $project->id,
            'Expense',
            'CREATE',
            "Mencatatkan pengeluaran belanja: '{$validated['title']}' sebesar Rp ".number_format($validated['amount'], 0, ',', '.')
        );

        return redirect()->route('user.projects.show', [$project->id, 'tab' => 'expenses'])
            ->with('success', 'Catatan pembelanjaan baru berhasil ditambahkan.');
    }

    /**
     * Hapus pengeluaran (Hanya untuk Finance)
     */
    public function destroyExpense(Project $project, ProjectExpense $expense): RedirectResponse
    {
        if (Auth::user()->role !== 'finance') {
            abort(403, 'Hanya Staff Keuangan yang dapat melakukan aksi ini.');
        }

        $expenseTitle = $expense->title;
        $expenseAmount = $expense->amount;

        if ($expense->receipt_file && Storage::disk('public')->exists($expense->receipt_file)) {
            Storage::disk('public')->delete($expense->receipt_file);
        }

        $expense->delete();

        // Rekalkulasi budget terpakai
        $totalApproved = $project->expenses()->where('status', 'Approved')->sum('amount');
        $project->update(['budget_spent' => $totalApproved]);

        // Catat Audit Log
        ProjectActivityLog::record(
            $project->id,
            'Expense',
            'DELETE',
            "Menghapus catatan belanja: '{$expenseTitle}' senilai Rp ".number_format($expenseAmount, 0, ',', '.')
        );

        return redirect()->route('user.projects.show', [$project->id, 'tab' => 'expenses'])
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    /**
     * AI Autocomplete Task: Menganalisis & memetakan prompt suara/teks ke field modal pengerjaan tugas
     */
    public function autoFillTask(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'type' => ['required', 'string', 'in:partial,final'],
        ]);

        try {
            $aiService = app(AIService::class);

            if ($request->type === 'partial') {
                $systemPrompt = "Tugasmu adalah menganalisis pesan progres tugas parsial dari pengguna dan memetakan datanya secara akurat ke dalam format JSON.\n\n"
                    ."Format output JSON yang harus kamu berikan:\n"
                    ."{\n"
                    ."  \"progress_percentage\": 1..99 (angka integer bulat mewakili persentase kemajuan tugas),\n"
                    ."  \"notes\": \"Catatan tentang apa saja yang telah diselesaikan\",\n"
                    ."  \"obstacles\": \"Kendala atau hambatan teknis yang dihadapi (isi kosong jika tidak ada)\",\n"
                    ."  \"attachment_url\": \"Link/URL demo/repository yang dicantumkan (isi kosong jika tidak ada)\"\n"
                    ."}\n\n"
                    .'Kamu HARUS merespon HANYA dengan dokumen JSON mentah tanpa format markdown (jangan pakai ```json atau blok kode lainnya).';
            } else {
                $systemPrompt = "Tugasmu adalah menganalisis pesan penyelesaian tugas 100% dari pengguna dan memetakan datanya secara akurat ke dalam format JSON.\n\n"
                    ."Format output JSON yang harus kamu berikan:\n"
                    ."{\n"
                    ."  \"submission_notes\": \"Rangkuman detail tentang deliverables/hasil akhir tugas (dalam format teks bersih)\",\n"
                    ."  \"obstacles_faced\": \"Kendala/hambatan yang dihadapi selama pengerjaan (isi kosong jika tidak ada)\",\n"
                    ."  \"self_evaluation\": \"Evaluasi diri dan saran perbaikan mandiri untuk tugas berikutnya\",\n"
                    ."  \"submission_link\": \"Link/URL demo/repository/hasil akhir (isi kosong jika tidak ada)\"\n"
                    ."}\n\n"
                    .'Kamu HARUS merespon HANYA dengan dokumen JSON mentah tanpa format markdown (jangan pakai ```json atau blok kode lainnya).';
            }

            $aiResponse = $aiService->chat([
                'system' => $systemPrompt,
                'message' => $request->message,
                'temperature' => 0.2,
            ]);

            $cleanContent = trim($aiResponse->content);
            if (str_starts_with($cleanContent, '```')) {
                $cleanContent = preg_replace('/^```(?:json)?|```$/m', '', $cleanContent);
                $cleanContent = trim($cleanContent);
            }

            $data = json_decode($cleanContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Gagal melakukan parsing JSON dari respon AI: '.$cleanContent);
            }

            return response()->json(array_merge([
                'success' => true,
            ], $data));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'LUNOU gagal menganalisis pesan progres Anda: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Interactive AI Chat about a specific Project
     */
    public function discussProjectAI(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $aiService = app(AIService::class);
            $allTasks = $project->tasks;
            $tasksSummary = '';
            foreach ($allTasks as $t) {
                $tasksSummary .= "- {$t->title} ({$t->status}, {$t->progress_percentage}%)\n";
            }
            $totalExpensesApproved = $project->expenses->where('status', 'Approved')->sum('amount');

            $systemPrompt = "Kamu adalah LUNOU, asisten wellness & manajemen proyek Yoimo yang peka dan mengerti detail proyek.\n"
                ."Kamu sedang berkonsultasi dengan anggota tim mengenai proyek berikut:\n"
                ."- Nama Proyek: {$project->name}\n"
                ."- Kategori: {$project->category}\n"
                ."- Tahapan: {$project->current_stage}\n"
                ."- Progres Keseluruhan: {$project->progress_percentage}%\n"
                .'- Keuangan: Budget Rp '.number_format($project->budget, 0, ',', '.').', Terpakai Rp '.number_format($totalExpensesApproved, 0, ',', '.')."\n"
                ."- Daftar Tugas:\n{$tasksSummary}\n\n"
                .'Jawab pertanyaan pengguna secara ramah, empati, penuh perhatian, dan berikan solusi/saran manajemen tugas yang cerdas dan praktis. Gunakan Bahasa Indonesia yang sopan dan suportif.';

            // Simpan riwayat chat user
            ProjectAiChat::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'role' => 'user',
                'message' => $request->message,
            ]);

            $res = $aiService->chat([
                'system' => $systemPrompt,
                'message' => $request->message,
                'temperature' => 0.7,
            ]);

            // Simpan riwayat chat assistant LUNOU
            ProjectAiChat::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'role' => 'assistant',
                'message' => $res->content,
            ]);

            return response()->json([
                'success' => true,
                'reply' => $res->content,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'LUNOU sedang kesulitan menghubungkan data proyek: '.$e->getMessage(),
            ], 500);
        }
    }
}
