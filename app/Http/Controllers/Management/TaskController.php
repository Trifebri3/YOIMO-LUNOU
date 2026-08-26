<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Models\ProjectActivityLog;
use App\Models\ProjectRoadmap;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'roadmaps'
        ]);
        
        $assignedUserIds = collect($project->team_matrix ?? [])->pluck('user_id')->unique();
        $teamMembers = User::whereIn('id', $assignedUserIds)->get();
        if ($teamMembers->isEmpty()) {
            $teamMembers = User::whereIn('role', ['user', 'finance'])->get();
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
            if (!preg_match('~^(?:f|ht)tps?://~i', $link)) {
                $request->merge(['attachment_link' => 'https://' . $link]);
            }
        }

        $validated = $request->validate([
            'project_roadmap_id'     => ['nullable', 'exists:project_roadmaps,id'],
            'assigned_to'            => ['nullable', 'exists:users,id'],
            'title'                  => ['required', 'string', 'max:255'],
            'description'            => ['nullable', 'string'],
            'attachment_link'        => ['nullable', 'string', 'max:255'],
            'attachment_file'        => ['nullable', 'file', 'max:15360'],
            'priority'               => ['required', 'in:Low,Medium,High,Urgent'],
            'due_date'               => ['nullable', 'date'],
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
                if ($task->assignee->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($task->assignee->email)->send(new \App\Mail\TaskNotificationMail($task, 'created'));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send Task created mail: " . $e->getMessage());
                    }
                }
                
                // WhatsApp Notification
                $msg = "📢 *TUGAS BARU DITUGASKAN*\n\n"
                    . "Halo, tugas baru telah ditugaskan kepada Anda:\n"
                    . "• *Proyek*: " . ($task->project->name ?? 'Project') . "\n"
                    . "• *Tugas*: {$task->title}\n"
                    . "• *Prioritas*: {$task->priority}\n"
                    . "• *Tenggat*: " . ($task->due_date ? date('d M Y', strtotime($task->due_date)) : 'Tanpa Tenggat') . "\n\n"
                    . "Silakan cek dashboard Yoimo untuk mulai mengerjakan. Semangat!";
                \App\Services\WhatsAppService::send($task->assignee->phone, $msg);
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
            return back()->with('error', 'Tugas ini sudah diambil oleh ' . ($task->assignee->name ?? 'user lain'));
        }

        $task->update([
            'assigned_to' => Auth::id(),
            'status'      => 'In Progress',
        ]);

        $task->load('assignee', 'project');
        if ($task->assignee) {
            if ($task->assignee->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($task->assignee->email)->send(new \App\Mail\TaskNotificationMail($task, 'updated'));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send Task claimed mail: " . $e->getMessage());
                }
            }
            
            // WhatsApp Notification
            $msg = "⚡ *TUGAS TELAH DIKLAIM*\n\n"
                . "Halo, Anda baru saja mengklaim/mengambil tugas berikut:\n"
                . "• *Proyek*: " . ($task->project->name ?? 'Project') . "\n"
                . "• *Tugas*: {$task->title}\n"
                . "• *Prioritas*: {$task->priority}\n"
                . "• *Tenggat*: " . ($task->due_date ? date('d M Y', strtotime($task->due_date)) : 'Tanpa Tenggat') . "\n\n"
                . "Selamat bekerja, LUNOU siap memantau progres Anda!";
            \App\Services\WhatsAppService::send($task->assignee->phone, $msg);
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Task', 'CLAIM', "Mengambil/Mengklaim penugasan tugas terbuka: '{$task->title}'");

        return back()->with('success', 'Anda berhasil mengambil tugas ini. Tugas sekarang menjadi tanggung jawab Anda.');
    }

    public function updateStatus(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:Todo,In Progress,Review,Completed']
        ]);

        $task->status = $request->status;

        if ($task->status === 'Completed' && $task->project_roadmap_id && $task->linked_objective_index !== null) {
            $roadmap = ProjectRoadmap::find($task->project_roadmap_id);
            if ($roadmap && !empty($roadmap->objectives)) {
                $objectives = $roadmap->objectives;
                $idx = $task->linked_objective_index;
                if (isset($objectives[$idx])) {
                    $objectives[$idx]['is_achieved'] = true;
                    
                    $total = count($objectives);
                    $achieved = collect($objectives)->where('is_achieved', true)->count();
                    $progress = $total > 0 ? round(($achieved / $total) * 100) : 0;

                    $roadmap->update([
                        'objectives'          => $objectives,
                        'progress_percentage' => $progress,
                    ]);
                }
            }
        }

        $task->save();

        if ($task->assigned_to) {
            $task->load('assignee', 'project');
            if ($task->assignee) {
                if ($task->assignee->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($task->assignee->email)->send(new \App\Mail\TaskNotificationMail($task, 'updated'));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send Task updated status mail: " . $e->getMessage());
                    }
                }
                
                // WhatsApp Notification
                $msg = "📝 *STATUS TUGAS DIPERBARUI*\n\n"
                    . "Halo, status tugas Anda telah diperbarui:\n"
                    . "• *Proyek*: " . ($task->project->name ?? 'Project') . "\n"
                    . "• *Tugas*: {$task->title}\n"
                    . "• *Status Baru*: {$task->status}\n"
                    . "• *Prioritas*: {$task->priority}\n\n"
                    . "Silakan cek dashboard Yoimo untuk melihat detailnya.";
                \App\Services\WhatsAppService::send($task->assignee->phone, $msg);
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
            'submission_link'  => ['nullable', 'url'],
            'submission_file'  => ['nullable', 'file', 'max:15360'],
        ]);

        $updateData = [
            'submission_notes' => $request->submission_notes,
            'submission_link'  => $request->submission_link,
            'status'           => 'Review',
            'submitted_at'     => now(),
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
            'decision'       => ['required', 'in:Approve,Reject'],
            'revision_notes' => ['nullable', 'string'],
        ]);

        if ($request->decision === 'Approve') {
            $task->update([
                'status'         => 'Completed',
                'revision_notes' => null,
            ]);

            // Otomatis centang indikator target roadmap jika terikat
            if ($task->project_roadmap_id && $task->linked_objective_index !== null) {
                $roadmap = ProjectRoadmap::find($task->project_roadmap_id);
                if ($roadmap && !empty($roadmap->objectives)) {
                    $objectives = $roadmap->objectives;
                    $idx = $task->linked_objective_index;
                    if (isset($objectives[$idx])) {
                        $objectives[$idx]['is_achieved'] = true;
                        $total = count($objectives);
                        $achieved = collect($objectives)->where('is_achieved', true)->count();
                        $progress = $total > 0 ? round(($achieved / $total) * 100) : 0;

                        $roadmap->update([
                            'objectives'          => $objectives,
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
                            \Illuminate\Support\Facades\Mail::to($task->assignee->email)->send(new \App\Mail\TaskNotificationMail($task, 'updated'));
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Failed to send Task approved mail: " . $e->getMessage());
                        }
                    }
                    
                    // WhatsApp Notification
                    $msg = "✅ *TUGAS SELESAI & DISETUJUI*\n\n"
                        . "Luar biasa! Laporan tugas Anda telah disetujui oleh manajemen:\n"
                        . "• *Proyek*: " . ($task->project->name ?? 'Project') . "\n"
                        . "• *Tugas*: {$task->title}\n"
                        . "• *Status*: Completed (Selesai)\n\n"
                        . "Terima kasih atas kerja keras Anda!";
                    \App\Services\WhatsAppService::send($task->assignee->phone, $msg);
                }
            }

            ProjectActivityLog::record($project->id, 'Task', 'UPDATE', "Menyetujui penyelesaian tugas: '{$task->title}'");
            return back()->with('success', 'Tugas berhasil disetujui dan berstatus Selesai (Completed).');
        } else {
            $task->update([
                'status'         => 'In Progress',
                'revision_notes' => $request->revision_notes ?? 'Perlu perbaikan sesuai standar kualitas.',
            ]);

            if ($task->assigned_to) {
                $task->load('assignee', 'project');
                if ($task->assignee) {
                    if ($task->assignee->email) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($task->assignee->email)->send(new \App\Mail\TaskNotificationMail($task, 'updated'));
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Failed to send Task rejected mail: " . $e->getMessage());
                        }
                    }
                    
                    // WhatsApp Notification
                    $msg = "⚠️ *TUGAS PERLU REVISI*\n\n"
                        . "Halo, tugas Anda membutuhkan revisi/perbaikan:\n"
                        . "• *Proyek*: " . ($task->project->name ?? 'Project') . "\n"
                        . "• *Tugas*: {$task->title}\n"
                        . "• *Catatan Revisi*: " . ($request->revision_notes ?? 'Perlu perbaikan.') . "\n\n"
                        . "Silakan cek dashboard Yoimo untuk melakukan perbaikan segera.";
                    \App\Services\WhatsAppService::send($task->assignee->phone, $msg);
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
            'Content-Disposition' => 'attachment; filename="tasks-project-' . Str::slug($project->name) . '.csv"',
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
            $priority = !empty($row[1]) && in_array($row[1], ['Low', 'Medium', 'High', 'Urgent']) ? $row[1] : 'Normal';
            $dueDate = !empty($row[2]) ? date('Y-m-d', strtotime($row[2])) : null;
            
            $assignedTo = null;
            if (!empty($row[3])) {
                $user = User::where('email', trim($row[3]))->first();
                if ($user) {
                    $assignedTo = $user->id;
                }
            }
            
            $description = !empty($row[4]) ? $row[4] : null;

            ProjectTask::create([
                'project_id'  => $project->id,
                'title'       => $title,
                'priority'    => $priority,
                'status'      => 'Todo',
                'due_date'    => $dueDate,
                'assigned_to' => $assignedTo,
                'description' => $description,
                'created_by'  => Auth::id(),
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
                'Buat form login dengan autentikasi multi-role menggunakan Laravel Fortify/Breeze.'
            ]);

            fputcsv($file, [
                'Slicing Landing Page & Responsive Test',
                'Normal',
                '2026-09-20',
                '',
                'Slicing UI/UX figma landing page ke template Blade HTML5 & CSS Vanilla.'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}