<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LocalAIController extends Controller
{
    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'prompt' => ['required', 'string', 'max:1000'],
        ]);

        $prompt = $request->prompt;

        // --- PARSING ALGORITMA (LOCAL DETERMINISTIC NLP) ---
        
        // 1. Parse Project Name
        // Pattern: "buat project [Nama]" or "project [Nama]"
        $projectName = null;
        if (preg_match('/(?:buat\s+project|project)\s+([^,\.\n\r]+)/i', $prompt, $matches)) {
            $projectName = trim($matches[1]);
        }
        
        // 2. Parse Client Name
        // Pattern: "client [Nama]" or "klien [Nama]"
        $clientName = null;
        if (preg_match('/(?:client|klien)\s+([^,\.\n\r]+)/i', $prompt, $matches)) {
            $clientName = trim($matches[1]);
        }

        // 3. Parse Budget
        // Pattern: "budget [Angka]" or "anggaran [Angka]"
        $budget = 0;
        if (preg_match('/(?:budget|anggaran)\s*([0-9\.\,]+)/i', $prompt, $matches)) {
            $budget = floatval(str_replace([',', '.'], '', $matches[1]));
        }

        // 4. Parse Tasks
        // Pattern: "tasks: [Task1, Task2]" or "tugas: [Task1, Task2]"
        $tasks = [];
        if (preg_match('/(?:tasks?|tugas)\s*:\s*([^.\n\r]+)/i', $prompt, $matches)) {
            $tasks = array_map('trim', explode(',', $matches[1]));
        }

        // 5. Parse Assignee Email
        // Pattern: "assign: [Email]" or "tugaskan ke [Email]"
        $assigneeEmail = null;
        if (preg_match('/(?:assign|tugaskan\s+ke)\s*:\s*([a-zA-Z0-9\._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $prompt, $matches)) {
            $assigneeEmail = trim($matches[1]);
        } elseif (preg_match('/(?:assign|tugaskan\s+ke)\s+([a-zA-Z0-9\._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $prompt, $matches)) {
            $assigneeEmail = trim($matches[1]);
        }

        // --- DATABASE PERSISTENCE ---

        // Set defaults if parsing was blank
        if (empty($projectName)) {
            $projectName = "Project Otomatis AI " . date('d M Y H:i');
        }

        // Get default company profile for this manager
        $companyId = CompanyProfile::where('manager_id', Auth::id())->value('id');
        if (!$companyId) {
            $companyId = CompanyProfile::first()->id ?? null;
        }

        // Create Project
        $project = Project::create([
            'company_profile_id' => $companyId,
            'created_by'         => Auth::id(),
            'name'               => $projectName,
            'client_name'        => $clientName ?? 'Internal Client',
            'category'           => 'Software Development',
            'status'             => 'Active',
            'priority'           => 'Medium',
            'current_stage'      => 'Planning',
            'budget'             => $budget,
        ]);

        // Resolve Assignee User
        $assignedUser = null;
        if ($assigneeEmail) {
            $assignedUser = User::where('email', $assigneeEmail)->first();
        }

        // Populate team matrix if user is resolved
        if ($assignedUser) {
            $project->update([
                'team_matrix' => [
                    [
                        'user_id' => $assignedUser->id,
                        'name'    => $assignedUser->name,
                        'role'    => 'Developer'
                    ]
                ]
            ]);
        }

        // Create Tasks
        $createdTasksCount = 0;
        foreach ($tasks as $taskTitle) {
            if (empty($taskTitle)) continue;

            ProjectTask::create([
                'project_id'  => $project->id,
                'title'       => $taskTitle,
                'priority'    => 'Normal',
                'status'      => 'Todo',
                'assigned_to' => $assignedUser->id ?? null,
                'created_by'  => Auth::id(),
            ]);
            $createdTasksCount++;
        }

        // Record Audit Log
        ProjectActivityLog::record($project->id, 'Project', 'CREATE', "AI Generator menerbitkan project '{$projectName}' dengan {$createdTasksCount} tugas");

        $assignedMsg = $assignedUser ? " & ditugaskan ke {$assignedUser->name}" : "";
        return redirect()->route('management.projects.tasks.index', $project->id)
            ->with('success', "AI Sukses: Project '{$projectName}' berhasil dibuat dengan {$createdTasksCount} tugas{$assignedMsg}.");
    }
}
