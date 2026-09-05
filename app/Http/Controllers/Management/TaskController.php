<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Mail\TaskNotificationMail;
use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectRoadmap;
use App\Models\ProjectTask;
use App\Models\User;
use App\Notifications\TaskNotification;
use App\Services\AIService;
use App\Services\GamificationService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Project $project): View
    {
        $project->load([
            'tasks' => function ($query) {
                $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
            },
            'tasks.assignee',
            'tasks.roadmap',
            'roadmaps',
        ]);

        $assignedUserIds = collect($project->team_matrix ?? [])->pluck('user_id')->unique();
        $teamMembers = User::whereIn('id', $assignedUserIds)->get();
        if ($teamMembers->isEmpty()) {
            $company = $project->company ?? CompanyProfile::find($project->company_profile_id);
            $teamMembers = $company
                ? $company->allMembers()
                    ->filter(fn ($u) => in_array($u->role, ['user', 'finance', 'management']))
                    ->values()
                : collect();
        }

        return view('management.projects.tasks.index', compact('project', 'teamMembers'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        if ($request->input('project_roadmap_id') === '') {
            $request->merge(['project_roadmap_id' => null]);
        }
        if ($request->input('assigned_to') === '') {
            $request->merge(['assigned_to' => null]);
        }
        if ($request->input('linked_objective_index') === '') {
            $request->merge(['linked_objective_index' => null]);
        }
        if ($request->filled('attachment_link')) {
            $link = $request->input('attachment_link');
            if (! preg_match('~^(?:f|ht)tps?://~i', $link)) {
                $request->merge(['attachment_link' => 'https://'.$link]);
            }
        }

        $validated = $request->validate([
            'project_roadmap_id' => ['nullable', 'exists:project_roadmaps,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'attachment_link' => ['nullable', 'string', 'max:255'],
            'attachment_file' => ['nullable', 'file', 'max:15360'],
            'priority' => ['required', 'in:Low,Medium,High,Urgent'],
            'due_date' => ['nullable', 'date'],
            'linked_objective_index' => ['nullable', 'integer'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'Todo';

        if ($request->hasFile('attachment_file')) {
            $validated['attachment_file'] = $request->file('attachment_file')->store('task_instructions', 'public');
        }

        $task = ProjectTask::create($validated);
        if ($task->assigned_to) {
            $task->load('assignee', 'project');
            if ($task->assignee) {
                $task->assignee->notify(new TaskNotification($task, 'created'));

                if ($task->assignee->email) {
                    try {
                        Mail::to($task->assignee->email)->send(new TaskNotificationMail($task, 'created'));
                    } catch (\Exception $e) {
                        Log::error('Failed to send Task created mail: '.$e->getMessage());
                    }
                }

                // WhatsApp Notification
                $msg = "📢 *TUGAS BARU DITUGASKAN*\n\n"
                    ."Halo, tugas baru telah ditugaskan kepada Anda:\n"
                    .'• *Proyek*: '.($task->project->name ?? 'Project')."\n"
                    ."• *Tugas*: {$task->title}\n"
                    ."• *Prioritas*: {$task->priority}\n"
                    .'• *Tenggat*: '.($task->due_date ? date('d M Y', strtotime($task->due_date)) : 'Tanpa Tenggat')."\n\n"
                    .'Silakan cek dashboard Yoimo untuk mulai mengerjakan. Semangat!';
                WhatsAppService::send($task->assignee->phone, $msg);
            }
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Task', 'CREATE', "Menerbitkan tugas baru: '{$validated['title']}'");

        return redirect()->route('management.projects.tasks.index', $project->id)
            ->with('success', 'Tugas baru berhasil diterbitkan.');
    }

    public function claim(Project $project, ProjectTask $task): RedirectResponse
    {
        if ($task->assigned_to !== null) {
            return back()->with('error', 'Tugas ini sudah diambil oleh '.($task->assignee->name ?? 'user lain'));
        }

        $task->update([
            'assigned_to' => Auth::id(),
            'status' => 'In Progress',
        ]);

        $task->load('assignee', 'project');
        if ($task->assignee) {
            // Notify project creator / manager
            $projectCreator = User::find($task->project->created_by);
            if ($projectCreator && $projectCreator->id !== Auth::id()) {
                $projectCreator->notify(new TaskNotification($task, 'claimed'));
            }
            $companyManagerId = $task->project->company->manager_id ?? null;
            if ($companyManagerId && $companyManagerId !== Auth::id() && $companyManagerId !== ($projectCreator->id ?? null)) {
                $companyManager = User::find($companyManagerId);
                if ($companyManager) {
                    $companyManager->notify(new TaskNotification($task, 'claimed'));
                }
            }

            if ($task->assignee->email) {
                try {
                    Mail::to($task->assignee->email)->send(new TaskNotificationMail($task, 'updated'));
                } catch (\Exception $e) {
                    Log::error('Failed to send Task claimed mail: '.$e->getMessage());
                }
            }

            // WhatsApp Notification
            $msg = "⚡ *TUGAS TELAH DIKLAIM*\n\n"
                ."Halo, Anda baru saja mengklaim/mengambil tugas berikut:\n"
                .'• *Proyek*: '.($task->project->name ?? 'Project')."\n"
                ."• *Tugas*: {$task->title}\n"
                ."• *Prioritas*: {$task->priority}\n"
                .'• *Tenggat*: '.($task->due_date ? date('d M Y', strtotime($task->due_date)) : 'Tanpa Tenggat')."\n\n"
                .'Selamat bekerja, LUNOU siap memantau progres Anda!';
            WhatsAppService::send($task->assignee->phone, $msg);
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Task', 'CLAIM', "Mengambil/Mengklaim penugasan tugas terbuka: '{$task->title}'");

        return back()->with('success', 'Anda berhasil mengambil tugas ini. Tugas sekarang menjadi tanggung jawab Anda.');
    }

    public function updateStatus(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:Todo,In Progress,Review,Completed'],
        ]);

        $task->status = $request->status;

        if ($task->status === 'Completed' && $task->project_roadmap_id && $task->linked_objective_index !== null) {
            $roadmap = ProjectRoadmap::find($task->project_roadmap_id);
            if ($roadmap && ! empty($roadmap->objectives)) {
                $objectives = $roadmap->objectives;
                $idx = $task->linked_objective_index;
                if (isset($objectives[$idx])) {
                    $objectives[$idx]['is_achieved'] = true;

                    $total = count($objectives);
                    $achieved = collect($objectives)->where('is_achieved', true)->count();
                    $progress = $total > 0 ? round(($achieved / $total) * 100) : 0;

                    $roadmap->update([
                        'objectives' => $objectives,
                        'progress_percentage' => $progress,
                    ]);
                }
            }
        }

        $task->save();

        if ($task->status === 'Completed' && $task->assigned_to) {
            GamificationService::awardTaskCompletion($task);
        }

        if ($task->assigned_to) {
            $task->load('assignee', 'project');
            if ($task->assignee) {
                // Notify the assignee if modified by someone else (e.g. manager)
                if (Auth::id() !== $task->assigned_to) {
                    $task->assignee->notify(new TaskNotification($task, 'updated'));
                }
                // Notify the creator/manager if modified by assignee
                if (Auth::id() === $task->assigned_to) {
                    $projectCreator = User::find($task->project->created_by);
                    if ($projectCreator && $projectCreator->id !== Auth::id()) {
                        $projectCreator->notify(new TaskNotification($task, 'updated'));
                    }
                }

                if ($task->assignee->email) {
                    try {
                        Mail::to($task->assignee->email)->send(new TaskNotificationMail($task, 'updated'));
                    } catch (\Exception $e) {
                        Log::error('Failed to send Task updated status mail: '.$e->getMessage());
                    }
                }

                // WhatsApp Notification
                $msg = "📝 *STATUS TUGAS DIPERBARUI*\n\n"
                    ."Halo, status tugas Anda telah diperbarui:\n"
                    .'• *Proyek*: '.($task->project->name ?? 'Project')."\n"
                    ."• *Tugas*: {$task->title}\n"
                    ."• *Status Baru*: {$task->status}\n"
                    ."• *Prioritas*: {$task->priority}\n\n"
                    .'Silakan cek dashboard Yoimo untuk melihat detailnya.';
                WhatsAppService::send($task->assignee->phone, $msg);
            }
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Task', 'UPDATE', "Mengubah status tugas '{$task->title}' menjadi {$request->status}");

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function submitReport(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        if ($task->assigned_to !== null && Auth::id() !== $task->assigned_to && Auth::user()->role !== 'management') {
            abort(403, 'Akses Ditolak: Anda bukan PIC yang ditugaskan untuk tugas ini.');
        }

        $request->validate([
            'submission_notes' => ['required', 'string'],
            'submission_link' => ['nullable', 'url'],
            'submission_file' => ['nullable', 'file', 'max:15360'],
        ]);

        $updateData = [
            'submission_notes' => $request->submission_notes,
            'submission_link' => $request->submission_link,
            'status' => 'Review',
            'submitted_at' => now(),
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

        // Notify project creator / manager
        $projectCreator = User::find($task->project->created_by);
        if ($projectCreator && $projectCreator->id !== Auth::id()) {
            $projectCreator->notify(new TaskNotification($task, 'submitted'));
        }
        $companyManagerId = $task->project->company->manager_id ?? null;
        if ($companyManagerId && $companyManagerId !== Auth::id() && $companyManagerId !== ($projectCreator->id ?? null)) {
            $companyManager = User::find($companyManagerId);
            if ($companyManager) {
                $companyManager->notify(new TaskNotification($task, 'submitted'));
            }
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Task', 'SUBMIT', "Mengirimkan laporan hasil kerja untuk tugas: '{$task->title}'");

        return back()->with('success', 'Laporan hasil tugas berhasil dikirimkan untuk direview.');
    }

    public function destroy(Project $project, ProjectTask $task): RedirectResponse
    {
        $taskTitle = $task->title;

        if ($task->submission_file && Storage::disk('public')->exists($task->submission_file)) {
            Storage::disk('public')->delete($task->submission_file);
        }
        if ($task->attachment_file && Storage::disk('public')->exists($task->attachment_file)) {
            Storage::disk('public')->delete($task->attachment_file);
        }

        $task->delete();

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Task', 'DELETE', "Menghapus tugas: '{$taskTitle}'");

        return redirect()->route('management.projects.tasks.index', $project->id)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function reviewTask(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $request->validate([
            'decision' => ['required', 'in:Approve,Reject'],
            'revision_notes' => ['nullable', 'string'],
        ]);

        if ($request->decision === 'Approve') {
            $task->update([
                'status' => 'Completed',
                'revision_notes' => null,
            ]);

            GamificationService::awardTaskCompletion($task);

            // Otomatis centang indikator target roadmap jika terikat
            if ($task->project_roadmap_id && $task->linked_objective_index !== null) {
                $roadmap = ProjectRoadmap::find($task->project_roadmap_id);
                if ($roadmap && ! empty($roadmap->objectives)) {
                    $objectives = $roadmap->objectives;
                    $idx = $task->linked_objective_index;
                    if (isset($objectives[$idx])) {
                        $objectives[$idx]['is_achieved'] = true;
                        $total = count($objectives);
                        $achieved = collect($objectives)->where('is_achieved', true)->count();
                        $progress = $total > 0 ? round(($achieved / $total) * 100) : 0;

                        $roadmap->update([
                            'objectives' => $objectives,
                            'progress_percentage' => $progress,
                        ]);
                    }
                }
            }

            if ($task->assigned_to) {
                $task->load('assignee', 'project');
                if ($task->assignee) {
                    if ($task->assignee->email) {
                        try {
                            Mail::to($task->assignee->email)->send(new TaskNotificationMail($task, 'updated'));
                        } catch (\Exception $e) {
                            Log::error('Failed to send Task approved mail: '.$e->getMessage());
                        }
                    }

                    // WhatsApp Notification
                    $msg = "✅ *TUGAS SELESAI & DISETUJUI*\n\n"
                        ."Luar biasa! Laporan tugas Anda telah disetujui oleh manajemen:\n"
                        .'• *Proyek*: '.($task->project->name ?? 'Project')."\n"
                        ."• *Tugas*: {$task->title}\n"
                        ."• *Status*: Completed (Selesai)\n\n"
                        .'Terima kasih atas kerja keras Anda!';
                    WhatsAppService::send($task->assignee->phone, $msg);
                }
            }

            ProjectActivityLog::record($project->id, 'Task', 'UPDATE', "Menyetujui penyelesaian tugas: '{$task->title}'");

            return back()->with('success', 'Tugas berhasil disetujui dan berstatus Selesai (Completed).');
        } else {
            $task->update([
                'status' => 'In Progress',
                'revision_notes' => $request->revision_notes ?? 'Perlu perbaikan sesuai standar kualitas.',
            ]);

            if ($task->assigned_to) {
                $task->load('assignee', 'project');
                if ($task->assignee) {
                    if ($task->assignee->email) {
                        try {
                            Mail::to($task->assignee->email)->send(new TaskNotificationMail($task, 'updated'));
                        } catch (\Exception $e) {
                            Log::error('Failed to send Task rejected mail: '.$e->getMessage());
                        }
                    }

                    // WhatsApp Notification
                    $msg = "⚠️ *TUGAS PERLU REVISI*\n\n"
                        ."Halo, tugas Anda membutuhkan revisi/perbaikan:\n"
                        .'• *Proyek*: '.($task->project->name ?? 'Project')."\n"
                        ."• *Tugas*: {$task->title}\n"
                        .'• *Catatan Revisi*: '.($request->revision_notes ?? 'Perlu perbaikan.')."\n\n"
                        .'Silakan cek dashboard Yoimo untuk melakukan perbaikan segera.';
                    WhatsAppService::send($task->assignee->phone, $msg);
                }
            }

            ProjectActivityLog::record($project->id, 'Task', 'UPDATE', "Meminta revisi/penolakan tugas: '{$task->title}'");

            return back()->with('success', 'Tugas dikembalikan ke tim untuk dilakukan revisi.');
        }
    }

    public function exportCsv(Project $project)
    {
        $tasks = $project->tasks()->with('assignee')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tasks-project-'.Str::slug($project->name).'.csv"',
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['title', 'priority', 'due_date', 'assigned_email', 'description']);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->title,
                    $task->priority,
                    $task->due_date ? $task->due_date->format('Y-m-d') : '',
                    $task->assignee->email ?? '',
                    $task->description ? strip_tags($task->description) : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // skip header

        $importedCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) {
                continue;
            }

            $title = $row[0];
            $priority = ! empty($row[1]) && in_array($row[1], ['Low', 'Medium', 'High', 'Urgent']) ? $row[1] : 'Normal';
            $dueDate = ! empty($row[2]) ? date('Y-m-d', strtotime($row[2])) : null;

            $assignedTo = null;
            if (! empty($row[3])) {
                $user = User::where('email', trim($row[3]))->first();
                if ($user) {
                    $assignedTo = $user->id;
                }
            }

            $description = ! empty($row[4]) ? $row[4] : null;

            ProjectTask::create([
                'project_id' => $project->id,
                'title' => $title,
                'priority' => $priority,
                'status' => 'Todo',
                'due_date' => $dueDate,
                'assigned_to' => $assignedTo,
                'description' => $description,
                'created_by' => Auth::id(),
            ]);

            $importedCount++;
        }

        fclose($handle);

        ProjectActivityLog::record($project->id, 'Task', 'IMPORT', "Mengimport {$importedCount} tugas dari file CSV");

        return back()->with('success', "Berhasil mengimport {$importedCount} tugas dari file CSV.");
    }

    public function downloadTemplate(Project $project)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-tasks-import.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['title', 'priority', 'due_date', 'assigned_email', 'description']);

            fputcsv($file, [
                'Implementasi Fitur Login Multi-role',
                'High',
                '2026-09-15',
                'developer@company.com',
                'Buat form login dengan autentikasi multi-role menggunakan Laravel Fortify/Breeze.',
            ]);

            fputcsv($file, [
                'Slicing Landing Page & Responsive Test',
                'Normal',
                '2026-09-20',
                '',
                'Slicing UI/UX figma landing page ke template Blade HTML5 & CSS Vanilla.',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function generateTasksAi(Request $request, Project $project): RedirectResponse
    {
        $roadmaps = ProjectRoadmap::where('project_id', $project->id)->orderBy('start_date')->get();

        if ($roadmaps->isEmpty()) {
            return back()->with('error', 'Silakan buat alur linimasa/roadmap proyek terlebih dahulu sebelum men-generate tugas otomatis.');
        }

        $projectName = $project->name;
        $projectBrief = $project->description ?: 'Tidak ada deskripsi detail.';
        $customInstruction = $request->input('instruction') ?: 'Tidak ada instruksi khusus.';

        // Build Roadmap details for prompt context
        $roadmapContext = '';
        foreach ($roadmaps as $idx => $rm) {
            $roadmapContext .= "FASE INDEX {$idx}:\n"
                ."- Judul: {$rm->title}\n"
                ."- Deskripsi: {$rm->description}\n"
                .'- Tanggal Mulai: '.$rm->start_date->format('Y-m-d')."\n"
                .'- Tanggal Selesai: '.$rm->end_date->format('Y-m-d')."\n"
                ."- Objectives/KPIs:\n";
            if (! empty($rm->objectives)) {
                foreach ($rm->objectives as $oIdx => $obj) {
                    $roadmapContext .= "  * KPI INDEX {$oIdx}: ".$obj['target']."\n";
                }
            } else {
                $roadmapContext .= "  * (Tidak ada KPI spesifik)\n";
            }
            $roadmapContext .= "\n";
        }

        $systemPrompt = "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Dalam merancang tugas, Anda wajib mengadopsi pola pikir Systems Thinking (melihat proyek sebagai kesatuan sistem yang saling terhubung), Design Thinking (berorientasi pada pemecahan masalah secara kreatif, empati pengguna, ideasi, dan pengujian berulang), serta Human-First (mengutamakan kebutuhan manusia/pengguna akhir, kejelasan instruksi kerja yang empati bagi tim pengembang, serta dampak positif aplikasi bagi penggunanya). Tugas Anda adalah menyebarkan/memecah target KPI linimasa proyek menjadi daftar penugasan (Tasks) yang sangat detail, granular, dan tidak generik untuk tim pengembang.\n\n"
            ."DETAIL PROYEK:\n"
            ."- Nama Proyek: {$projectName}\n"
            ."- Deskripsi/Brief Proyek: {$projectBrief}\n\n"
            ."ALUR LINIMASA (ROADMAP) SAAT INI:\n"
            ."{$roadmapContext}\n"
            ."INSTRUKSI KHUSUS DARI USER:\n"
            ."{$customInstruction}\n\n"
            ."ATURAN GENERATE TUGAS (TASKS):\n"
            ."1. Untuk setiap KPI/Objective pada masing-masing fase, pecahkan menjadi 1 hingga 3 tugas yang sangat DETAIL dan SPESIFIK. Jangan gunakan judul/deskripsi generik (misal: jangan gunakan 'Desain logo' saja, melainkan 'Membuat 3 opsi sketsa logo komunitas Kelompok Wanita Tani dengan variasi warna alam').\n"
            ."2. Setiap tugas harus dikaitkan dengan fase linimasa yang tepat (`roadmap_phase_index`) dan indeks KPI yang relevan (`linked_objective_index`).\n"
            ."3. Tenggat waktu pengerjaan tugas (`due_date`) wajib berada di dalam rentang tanggal fase yang bersangkutan, dan disarankan disamakan dengan Tanggal Selesai fase tersebut.\n"
            ."4. Prioritas tugas ditentukan secara logis: 'Low', 'Medium', 'High', atau 'Urgent'.\n"
            ."5. Format deskripsi tugas wajib memberikan instruksi langkah-demi-langkah (step-by-step) yang konkret untuk mempermudah pegawai.\n"
            ."6. Kembalikan hasilnya dalam format JSON murni berupa array objek dengan struktur:\n"
            ."[\n"
            ."  {\n"
            ."    \"roadmap_phase_index\": 0,\n"
            ."    \"linked_objective_index\": 1,\n"
            ."    \"title\": \"Judul Tugas Rinci\",\n"
            ."    \"description\": \"Deskripsi instruksi langkah-demi-langkah...\",\n"
            ."    \"priority\": \"Medium\",\n"
            ."    \"due_date\": \"YYYY-MM-DD\"\n"
            ."  },\n"
            ."  ...\n"
            ."]\n"
            .'Jangan sertakan markdown, pembungkus ```json, atau penjelasan apapun selain JSON tersebut.';

        try {
            $aiService = app(AIService::class);
            $response = $aiService->chat([
                'system' => 'Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager. Tugas Anda adalah memecah roadmap proyek IT menjadi daftar penugasan detail kuantitatif berformat JSON murni.',
                'message' => $systemPrompt,
                'temperature' => 0.2,
            ]);

            $content = trim($response->content);
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
            }
            $content = trim($content);

            $tasks = json_decode($content, true);

            if (! is_array($tasks)) {
                throw new \Exception('Format respon AI tidak valid: '.$content);
            }

            $createdCount = 0;
            foreach ($tasks as $taskData) {
                $phaseIdx = $taskData['roadmap_phase_index'] ?? null;
                $roadmapId = null;
                if ($phaseIdx !== null && isset($roadmaps[$phaseIdx])) {
                    $roadmapId = $roadmaps[$phaseIdx]->id;
                }

                ProjectTask::create([
                    'project_id' => $project->id,
                    'project_roadmap_id' => $roadmapId,
                    'assigned_to' => null, // Unassigned
                    'created_by' => Auth::id(),
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'priority' => $taskData['priority'] ?? 'Medium',
                    'status' => 'Todo',
                    'due_date' => $taskData['due_date'] ?? null,
                    'linked_objective_index' => $taskData['linked_objective_index'] ?? null,
                ]);
                $createdCount++;
            }

            // Catat Audit Log
            ProjectActivityLog::record($project->id, 'Task', 'CREATE', "Men-generate secara otomatis {$createdCount} daftar tugas terperinci berdasarkan alur linimasa menggunakan LUNOU AI.");

            return redirect()->route('management.projects.tasks.index', $project->id)
                ->with('success', "Berhasil men-generate {$createdCount} tugas baru secara otomatis menggunakan LUNOU AI.");

        } catch (\Exception $e) {
            Log::error('Failed to generate Tasks via AI: '.$e->getMessage());

            return back()->with('error', 'Gagal memproses pembuatan tugas otomatis: '.$e->getMessage());
        }
    }

    public function addSingleTaskAi(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $roadmaps = ProjectRoadmap::where('project_id', $project->id)->orderBy('start_date')->get();
        $projectName = $project->name;
        $projectBrief = $project->description ?: 'Tidak ada deskripsi detail.';
        $promptText = $request->input('prompt');

        // Build Roadmap details for prompt context
        $roadmapContext = '';
        foreach ($roadmaps as $idx => $rm) {
            $roadmapContext .= "FASE INDEX {$idx} (ID: {$rm->id}):\n"
                ."- Judul: {$rm->title}\n"
                .'- Tanggal Mulai: '.$rm->start_date->format('Y-m-d')."\n"
                .'- Tanggal Selesai: '.$rm->end_date->format('Y-m-d')."\n"
                ."- Objectives/KPIs:\n";
            if (! empty($rm->objectives)) {
                foreach ($rm->objectives as $oIdx => $obj) {
                    $roadmapContext .= "  * KPI INDEX {$oIdx}: ".$obj['target']."\n";
                }
            }
            $roadmapContext .= "\n";
        }

        $systemPrompt = "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Anda menguasai Systems Thinking, Design Thinking, dan Human-First.\n\n"
            ."Tugas Anda adalah merancang SATU tugas (Task) terperinci berdasarkan permintaan pengguna (prompt) dan konteks proyek berikut.\n\n"
            ."DETAIL PROYEK:\n"
            ."- Nama Proyek: {$projectName}\n"
            ."- Deskripsi/Brief Proyek: {$projectBrief}\n\n"
            ."ALUR LINIMASA (ROADMAP) PROYEK:\n"
            ."{$roadmapContext}\n"
            ."PERMINTAAN USER UNTUK TUGAS INI:\n"
            ."{$promptText}\n\n"
            ."ATURAN MERANCANG TUGAS:\n"
            ."1. Buat tugas yang sangat spesifik dan detail (tidak boleh generik).\n"
            ."2. Tentukan prioritas ('Low', 'Medium', 'High', atau 'Urgent') secara logis berdasarkan urgensi yang disebutkan user atau jenis tugas.\n"
            ."3. Tentukan due_date dalam format YYYY-MM-DD. Jika user menyebutkan waktu (misal 'akhir minggu ini'), hitunglah secara relatif dari tanggal hari ini yaitu ".now()->format('Y-m-d').". Jika tugas dapat dikaitkan dengan fase linimasa tertentu, pastikan due_date berada di dalam rentang fase tersebut.\n"
            ."4. Jika tugas berkaitan dengan fase linimasa tertentu, tentukan `roadmap_phase_index` (indeks fase, mulai dari 0) dan `linked_objective_index` (indeks KPI dalam fase tersebut, mulai dari 0). Jika tidak cocok dengan fase mana pun, kosongkan (null).\n"
            ."5. Format deskripsi tugas wajib berupa instruksi kerja langkah-demi-langkah (step-by-step) yang konkret.\n"
            ."6. Kembalikan hasilnya dalam format JSON murni dengan struktur:\n"
            ."{\n"
            ."  \"roadmap_phase_index\": 0,\n"
            ."  \"linked_objective_index\": 1,\n"
            ."  \"title\": \"Judul Tugas\",\n"
            ."  \"description\": \"Deskripsi detail tugas...\",\n"
            ."  \"priority\": \"High\",\n"
            ."  \"due_date\": \"YYYY-MM-DD\"\n"
            ."}\n"
            .'Jangan sertakan markdown, pembungkus ```json, atau penjelasan lainnya.';

        try {
            $aiService = app(AIService::class);
            $response = $aiService->chat([
                'system' => 'Anda adalah Asisten Proyek AI bernama LUNOU. Tugas Anda adalah menganalisis prompt dan merancang satu tugas terperinci berformat JSON murni.',
                'message' => $systemPrompt,
                'temperature' => 0.2,
            ]);

            $content = trim($response->content);
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
            }
            $content = trim($content);

            $taskData = json_decode($content, true);

            if (! is_array($taskData) || ! isset($taskData['title'])) {
                throw new \Exception('Format respon AI tidak valid: '.$content);
            }

            $phaseIdx = $taskData['roadmap_phase_index'] ?? null;
            $roadmapId = null;
            if ($phaseIdx !== null && isset($roadmaps[$phaseIdx])) {
                $roadmapId = $roadmaps[$phaseIdx]->id;
            }

            $task = ProjectTask::create([
                'project_id' => $project->id,
                'project_roadmap_id' => $roadmapId,
                'assigned_to' => null,
                'created_by' => Auth::id(),
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'priority' => $taskData['priority'] ?? 'Medium',
                'status' => 'Todo',
                'due_date' => $taskData['due_date'] ?? null,
                'linked_objective_index' => $taskData['linked_objective_index'] ?? null,
            ]);

            // Catat Audit Log
            ProjectActivityLog::record($project->id, 'Task', 'CREATE', "Membuat satu tugas baru secara otomatis menggunakan LUNOU AI: [{$task->title}].");

            return redirect()->route('management.projects.tasks.index', $project->id)
                ->with('success', 'Berhasil menambahkan tugas "'.$task->title.'" secara otomatis menggunakan LUNOU AI.');

        } catch (\Exception $e) {
            Log::error('Failed to add single Task via AI: '.$e->getMessage());

            return back()->with('error', 'Gagal memproses pembuatan tugas otomatis: '.$e->getMessage());
        }
    }

    /**
     * Direct assign task to team member
     */
    public function assign(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $prevAssigneeId = $task->assigned_to;
        $newAssigneeId = $request->input('assigned_to');

        $task->update([
            'assigned_to' => $newAssigneeId,
        ]);

        // Trigger notification if assigned to someone new
        if ($newAssigneeId && $newAssigneeId != $prevAssigneeId) {
            $assignee = User::find($newAssigneeId);
            if ($assignee) {
                $assignee->notify(new TaskNotification($task, 'assigned'));
            }
        }

        return back()->with('success', 'Petugas tugas berhasil diperbarui.');
    }
}
