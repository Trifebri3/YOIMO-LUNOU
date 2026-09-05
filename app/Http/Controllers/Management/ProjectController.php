<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectAiChat;
use App\Models\User;
use App\Services\AIService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        // Ambil project yang berada di bawah perusahaan yang dikelola user management ini
        $myCompanyIds = CompanyProfile::where('manager_id', Auth::id())->pluck('id');

        $isArchived = $request->query('filter') === 'archived';

        $projects = Project::where('is_archived', $isArchived)
            ->where(function ($query) use ($myCompanyIds) {
                $query->whereIn('company_profile_id', $myCompanyIds)
                    ->orWhere('created_by', Auth::id());
            })
            ->with(['company', 'creator'])
            ->latest()
            ->paginate(10);

        $activeCount = Project::where('is_archived', false)
            ->where(function ($query) use ($myCompanyIds) {
                $query->whereIn('company_profile_id', $myCompanyIds)
                    ->orWhere('created_by', Auth::id());
            })->count();

        $archivedCount = Project::where('is_archived', true)
            ->where(function ($query) use ($myCompanyIds) {
                $query->whereIn('company_profile_id', $myCompanyIds)
                    ->orWhere('created_by', Auth::id());
            })->count();

        return view('management.projects.index', compact('projects', 'isArchived', 'activeCount', 'archivedCount'));
    }

    public function create(Request $request): View
    {
        $selectedCompany = null;
        if ($request->filled('company_id')) {
            $selectedCompany = CompanyProfile::where('id', $request->company_id)
                ->where('manager_id', Auth::id())
                ->first();
        }

        if (! $selectedCompany) {
            $selectedCompany = CompanyProfile::where('manager_id', Auth::id())->first();
        }

        $companyId = $selectedCompany ? $selectedCompany->id : null;

        // Ambil semua user yang bergabung di company ini (pivot, legacy, manager)
        $teamMembers = $selectedCompany
            ? $selectedCompany->allMembers()
                ->filter(fn ($u) => in_array($u->role, ['user', 'finance', 'management']))
                ->sortBy('name')
                ->values()
            : collect();

        return view('management.projects.create', compact('selectedCompany', 'teamMembers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_profile_id' => ['nullable', 'exists:company_profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:Planning,Active,On Hold,Completed'],
            'current_stage' => ['nullable', 'in:Planning,Development,Review,Revision,Launch'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],
            'budget' => ['nullable', 'numeric'],
            'budget_spent' => ['nullable', 'numeric'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'problem_statement' => ['nullable', 'string'],
            'project_goals' => ['nullable', 'string'],
            'expected_outputs' => ['nullable', 'string'],
            'scope_included' => ['nullable', 'array'],
            'scope_excluded' => ['nullable', 'array'],
            'milestones' => ['nullable', 'array'],
            'deliverables' => ['nullable', 'array'],
            'team_matrix' => ['nullable', 'array'],
            'meeting_notes' => ['nullable', 'array'],
            'doc_files.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['budget'] = $request->input('budget', 0);
        $validated['budget_spent'] = $request->input('budget_spent', 0);
        $validated['progress_percentage'] = $request->input('progress_percentage', 0);

        $validated['company_profile_id'] = $request->filled('company_profile_id')
            ? $request->company_profile_id
            : CompanyProfile::where('manager_id', Auth::id())->value('id');

        // Upload Asset / Dokumen
        $documents = $request->input('documents', []);
        if ($request->hasFile('doc_files')) {
            foreach ($request->file('doc_files') as $idx => $file) {
                $path = $file->store('project_docs', 'public');
                $documents[$idx]['file_url'] = $path;
            }
        }
        $validated['documents'] = array_values($documents);
        $validated['team_matrix'] = array_values($request->input('team_matrix', []));

        $project = Project::create($validated);

        return redirect()->route('management.projects.show', $project->id)
            ->with('success', 'Project baru berhasil dibuat!');
    }

    public function show(Project $project): View
    {
        $project->load([
            'company',
            'creator',
            'roadmaps',
            'tasks' => function ($query) {
                $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
            },
            'tasks.assignee',
            'tasks.roadmap',
        ]);

        $userId = Auth::id();
        $allTasks = $project->tasks;
        $totalExpensesApproved = $project->expenses ? $project->expenses->where('status', 'Approved')->sum('amount') : 0;

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

        return view('management.projects.show', compact('project', 'projectAiInsight', 'projectAiChats'));
    }

    public function edit(Project $project): View
    {
        $companies = CompanyProfile::where('manager_id', Auth::id())->get();

        $companyId = $project->company_profile_id;

        // Ambil semua user yang bergabung di company ini (pivot, legacy, manager)
        $company = $project->company ?? CompanyProfile::find($companyId);
        $teamMembers = $company
            ? $company->allMembers()
                ->filter(fn ($u) => in_array($u->role, ['user', 'finance', 'management']))
                ->sortBy('name')
                ->values()
            : collect();

        return view('management.projects.edit', compact('project', 'companies', 'teamMembers'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'company_profile_id' => ['nullable', 'exists:company_profiles,id'],
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'current_stage' => ['nullable', 'in:Planning,Development,Review,Revision,Launch'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],
            'budget' => ['nullable', 'numeric'],
            'budget_spent' => ['nullable', 'numeric'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'problem_statement' => ['nullable', 'string'],
            'project_goals' => ['nullable', 'string'],
            'expected_outputs' => ['nullable', 'string'],
            'scope_included' => ['nullable', 'array'],
            'scope_excluded' => ['nullable', 'array'],
            'milestones' => ['nullable', 'array'],
            'deliverables' => ['nullable', 'array'],
            'team_matrix' => ['nullable', 'array'],
            'meeting_notes' => ['nullable', 'array'],
            'doc_files.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $documents = $request->input('documents', []);
        if ($request->hasFile('doc_files')) {
            foreach ($request->file('doc_files') as $idx => $file) {
                $path = $file->store('project_docs', 'public');
                $documents[$idx]['file_url'] = $path;
            }
        }

        // Pertahankan dokumen lama
        if ($project->documents) {
            foreach ($documents as $idx => $doc) {
                if (empty($doc['file_url']) && isset($project->documents[$idx]['file_url'])) {
                    $documents[$idx]['file_url'] = $project->documents[$idx]['file_url'];
                }
            }
        }
        $validated['documents'] = array_values($documents);
        $validated['team_matrix'] = array_values($request->input('team_matrix', []));

        $project->update($validated);

        return redirect()->route('management.projects.show', $project->id)
            ->with('success', 'Project berhasil diperbarui!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('management.projects.index')->with('success', 'Project telah dihapus.');
    }

    /**
     * Fitur Automation AI Project Generator via Gemini API
     */
    public function generateAi(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $systemPrompt = 'Kamu adalah Project Manager & Systems Architect ahli. Pengguna akan memberikan deskripsi singkat ide project. '
            .'Buatkan struktur project lengkap dalam format JSON MURNI (tanpa markdown backtick ```json) dengan keys: '
            .'name (string nama project profesional), client_name (string nama klien/organisasi), category (string), '
            .'priority (string: Low, Medium, High, atau Urgent), start_date (string tanggal mulai format YYYY-MM-DD, gunakan tanggal hari ini '.date('Y-m-d').' jika tidak disebutkan), '
            .'deadline (string tanggal tenggat format YYYY-MM-DD jika bisa disimpulkan, atau kosongkan), '
            .'budget (numeric angka bulat total anggaran rupiah proyek, misal 150000000), '
            .'problem_statement (string masalah yang diselesaikan), project_goals (string tujuan project), expected_outputs (string hasil akhir), '
            .'scope_included (array of string 4-6 item fitur yang termasuk), scope_excluded (array of string 2-3 item batasan), '
            .'deliverables (array of string 3-5 item deliverable), '
            ."milestones (array of object berisi title dan target_days dari start_date, misal [{'title': 'SRS & Figma Wireframe', 'target_days': 7}, {'title': 'MVP Backend & Core API', 'target_days': 21}, {'title': 'Integration & UAT', 'target_days': 35}]). "
            .'Pastikan bahasa Indonesia profesional.';

        try {
            $aiService = app(AIService::class);
            $aiResponse = $aiService->chat([
                'system' => $systemPrompt,
                'message' => 'Ide Project Pengguna: '.$request->prompt,
                'temperature' => 0.4,
            ]);

            $jsonString = trim($aiResponse->content);
            if (strpos($jsonString, '```') === 0) {
                $jsonString = preg_replace('/^```(?:json)?\n|```$/', '', $jsonString);
            }
            $jsonString = trim($jsonString);

            $aiData = json_decode($jsonString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Respon AI tidak valid JSON: '.json_last_error_msg()."\nRespon mentah: ".$aiResponse->content,
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $aiData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem AI: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * AI Task Generator combining project context (Roadmap, Goals, Brief, Team Members)
     */
    public function generateTaskAi(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $project->load('roadmaps');

        // Available team members
        $teamMembers = User::whereIn('role', ['user', 'finance'])
            ->get(['id', 'name', 'role', 'position'])
            ->toArray();

        $roadmapsInfo = [];
        foreach ($project->roadmaps as $rm) {
            $roadmapsInfo[] = [
                'id' => $rm->id,
                'title' => $rm->title,
                'start_date' => $rm->start_date->format('Y-m-d'),
                'end_date' => $rm->end_date->format('Y-m-d'),
                'objectives' => $rm->objectives ?? [],
            ];
        }

        $systemPrompt = 'Kamu adalah Project Manager & Systems Architect ahli di Yoimo Workspace. '
            .'Tugasmu adalah menganalisis ide/permintaan pembuatan tugas dari user dan memetakan datanya ke context project saat ini. '
            ."CONTEXT PROJECT:\n"
            ."- Nama Project: {$project->name}\n"
            ."- Masalah: {$project->problem_statement}\n"
            ."- Gol Project: {$project->project_goals}\n"
            ."- Output Diharapkan: {$project->expected_outputs}\n"
            .'- Scope In: '.json_encode($project->scope_included ?? [])."\n"
            .'- Scope Out: '.json_encode($project->scope_excluded ?? [])."\n"
            .'- Milestones: '.json_encode($project->milestones ?? [])."\n"
            .'- Roadmaps / Linimasa Fase: '.json_encode($roadmapsInfo)."\n"
            .'- Anggota Tim Tersedia: '.json_encode($teamMembers)."\n\n"
            ."Buatlah detail tugas terstruktur dalam format JSON MURNI (tanpa markdown backtick ```json) dengan keys berikut:\n"
            ."1. title (string judul tugas singkat & jelas)\n"
            ."2. description (string HTML instruksi detail untuk kerja, terstruktur dengan sub-heading dan bullet points checklist, disesuaikan dengan gol/scope project)\n"
            ."3. priority (string: Low, Medium, High, atau Urgent)\n"
            ."4. due_date (string format YYYY-MM-DD, pastikan berada dalam rentang roadmap jika dikaitkan)\n"
            ."5. assigned_to (integer ID user yang paling cocok ditugaskan berdasarkan nama/peran yang disebutkan oleh user, jika tidak disebutkan/tidak ada yang cocok gunakan null)\n"
            ."6. project_roadmap_id (integer ID roadmap/fase linimasa yang paling relevan dengan tugas ini, atau null jika tidak terkait)\n"
            ."7. linked_objective_index (integer index objective dari roadmap terkait, mulai dari 0, atau null jika tidak terkait)\n\n"
            .'Pastikan format output berupa JSON valid dan bahasa Indonesia profesional.';

        try {
            $aiService = app(AIService::class);
            $aiResponse = $aiService->chat([
                'system' => $systemPrompt,
                'message' => 'Permintaan Pembuatan Tugas: '.$request->prompt,
                'temperature' => 0.3,
            ]);

            $jsonString = trim($aiResponse->content);
            if (strpos($jsonString, '```') === 0) {
                $jsonString = preg_replace('/^```(?:json)?\n|```$/', '', $jsonString);
            }
            $jsonString = trim($jsonString);

            $aiData = json_decode($jsonString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Respon AI tidak valid JSON: '.json_last_error_msg()."\nRespon mentah: ".$aiResponse->content,
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $aiData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem AI: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * FUNGSI BARU: Update Team Matrix secara langsung (Quick Assign Team)
     */
    public function assignTeam(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'team_matrix' => ['nullable', 'array'],
            'team_matrix.*.user_id' => ['required', 'exists:users,id'],
            'team_matrix.*.role_title' => ['required', 'string', 'max:100'],
        ]);

        $project->update([
            'team_matrix' => array_values($request->input('team_matrix', [])),
        ]);

        return back()->with('success', 'Daftar tim project berhasil diperbarui.');
    }

    /**
     * Halaman Konfigurasi Khusus Portofolio Publik Project
     */
    public function editPortfolio(Project $project): View
    {
        return view('management.projects.portfolio_settings', compact('project'));
    }

    /**
     * Simpan Pengaturan Portofolio Publik Project
     */
    public function updatePortfolio(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'is_showcased' => ['nullable', 'boolean'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'solution_statement' => ['nullable', 'string'],
            'result_statement' => ['nullable', 'string'],
            'services_rendered' => ['nullable', 'array'],
            'tech_stacks' => ['nullable', 'array'],
            'project_cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'client_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'new_gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'testimonial_author' => ['nullable', 'string', 'max:255'],
            'testimonial_role' => ['nullable', 'string', 'max:255'],
            'testimonial_content' => ['nullable', 'string'],
            'testimonial_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $updateData = [
            'is_showcased' => $request->has('is_showcased'),
            'demo_url' => $request->demo_url,
            'short_description' => $request->short_description,
            'solution_statement' => $request->solution_statement,
            'result_statement' => $request->result_statement,
            'services_rendered' => array_values($request->input('services_rendered', [])),
            'tech_stacks' => array_values($request->input('tech_stacks', [])),
        ];

        // Upload Cover Project
        if ($request->hasFile('project_cover')) {
            if ($project->project_cover && Storage::disk('public')->exists($project->project_cover)) {
                Storage::disk('public')->delete($project->project_cover);
            }
            $updateData['project_cover'] = $request->file('project_cover')->store('portfolios/covers', 'public');
        }

        // Upload Logo Client
        if ($request->hasFile('client_logo')) {
            if ($project->client_logo && Storage::disk('public')->exists($project->client_logo)) {
                Storage::disk('public')->delete($project->client_logo);
            }
            $updateData['client_logo'] = $request->file('client_logo')->store('portfolios/clients', 'public');
        }

        // Upload Galeri Gambar
        $existingGallery = $project->gallery_images ?? [];
        if ($request->hasFile('new_gallery')) {
            foreach ($request->file('new_gallery') as $gFile) {
                $existingGallery[] = $gFile->store('portfolios/galleries', 'public');
            }
        }
        $updateData['gallery_images'] = array_values($existingGallery);

        // Testimonial Client
        if ($request->filled('testimonial_content')) {
            $updateData['client_testimonial'] = [
                'author' => $request->testimonial_author,
                'role' => $request->testimonial_role,
                'content' => $request->testimonial_content,
                'rating' => $request->input('testimonial_rating', 5),
            ];
        }

        $project->update($updateData);

        return redirect()->route('management.projects.show', $project->id)
            ->with('success', 'Pengaturan Showcase Portofolio berhasil disimpan.');
    }

    // Tambahkan di dalam class Project
    public function agendas()
    {
        return $this->hasMany(ProjectAgenda::class, 'project_id')->orderBy('start_date', 'asc');
    }

    public function exportCsv()
    {
        $myCompanyIds = CompanyProfile::where('manager_id', Auth::id())->pluck('id');
        $projects = Project::whereIn('company_profile_id', $myCompanyIds)
            ->orWhere('created_by', Auth::id())
            ->with('company')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects-export.csv"',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'client_name', 'category', 'status', 'priority', 'current_stage', 'start_date', 'deadline', 'budget', 'problem_statement', 'project_goals', 'expected_outputs']);

            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->name,
                    $project->client_name,
                    $project->category,
                    $project->status,
                    $project->priority,
                    $project->current_stage,
                    $project->start_date ? $project->start_date->format('Y-m-d') : '',
                    $project->deadline ? $project->deadline->format('Y-m-d') : '',
                    $project->budget,
                    $project->problem_statement,
                    $project->project_goals,
                    $project->expected_outputs,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $companyId = CompanyProfile::where('manager_id', Auth::id())->value('id');
        if (! $companyId) {
            $companyId = CompanyProfile::first()->id ?? null;
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // skip header

        $importedCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) {
                continue;
            }

            $name = $row[0];
            $clientName = ! empty($row[1]) ? $row[1] : null;
            $category = ! empty($row[2]) ? $row[2] : 'Software Development';
            $status = ! empty($row[3]) && in_array($row[3], ['Planning', 'Active', 'On Hold', 'Completed']) ? $row[3] : 'Planning';
            $priority = ! empty($row[4]) && in_array($row[4], ['Low', 'Medium', 'High', 'Urgent']) ? $row[4] : 'Medium';
            $stage = ! empty($row[5]) && in_array($row[5], ['Planning', 'Development', 'Review', 'Revision', 'Launch']) ? $row[5] : 'Planning';

            $startDate = ! empty($row[6]) ? date('Y-m-d', strtotime($row[6])) : null;
            $deadline = ! empty($row[7]) ? date('Y-m-d', strtotime($row[7])) : null;

            $budget = ! empty($row[8]) ? floatval($row[8]) : 0;
            $problem = ! empty($row[9]) ? $row[9] : null;
            $goals = ! empty($row[10]) ? $row[10] : null;
            $outputs = ! empty($row[11]) ? $row[11] : null;

            Project::create([
                'company_profile_id' => $companyId,
                'created_by' => Auth::id(),
                'name' => $name,
                'client_name' => $clientName,
                'category' => $category,
                'status' => $status,
                'priority' => $priority,
                'current_stage' => $stage,
                'start_date' => $startDate,
                'deadline' => $deadline,
                'budget' => $budget,
                'problem_statement' => $problem,
                'project_goals' => $goals,
                'expected_outputs' => $outputs,
            ]);

            $importedCount++;
        }

        fclose($handle);

        return redirect()->route('management.projects.index')
            ->with('success', "Berhasil mengimport {$importedCount} proyek dari file CSV.");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-projects-import.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'client_name', 'category', 'status', 'priority', 'current_stage', 'start_date', 'deadline', 'budget', 'problem_statement', 'project_goals', 'expected_outputs']);

            fputcsv($file, [
                'Website Showcase E-Commerce PT ABC',
                'PT ABC Pratama',
                'Software Development',
                'Planning',
                'High',
                'Planning',
                '2026-09-01',
                '2026-12-31',
                '150000000',
                'Klien membutuhkan platform untuk menampilkan catalog produk secara interaktif.',
                'Meningkatkan conversion rate catalog sebesar 20%.',
                'Website catalog e-commerce yang fully responsive.',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function generatePortfolioAi(Request $request, Project $project)
    {
        $userId = Auth::id();
        if ($project->created_by !== $userId && ($project->company && $project->company->manager_id !== $userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to project',
            ], 403);
        }

        $promptText = $request->input('prompt');

        try {
            $aiService = app(AIService::class);

            $systemInstruction = "Kamu adalah LUNOU, asisten AI portofolio Yoimo. Tugasmu adalah menganalisis rincian proyek dan menyusun data publikasi portofolio (Short Description, Solution Statement, Result Statement, Services Rendered, dan Tech Stacks).\n\n"
                ."Response HARUS berupa objek JSON dengan key berikut:\n"
                ."- short_description: Ringkasan 1-2 kalimat untuk kartu portofolio.\n"
                ."- solution_statement: Narasi bagaimana sistem memecahkan kendala.\n"
                ."- result_statement: Hasil, dampak, metrik ROI, atau output yang dicapai.\n"
                ."- services_rendered: array berisi 1-4 string layanan yang dikerjakan (contoh: ['UI/UX Design', 'Web Development']).\n"
                ."- tech_stacks: array berisi 1-5 string teknologi yang digunakan (contoh: ['Laravel 11', 'Tailwind CSS', 'Alpine.js']).\n\n"
                .'Pastikan format response Anda hanya berupa raw JSON valid tanpa markdown formatting atau pembungkus kode.';

            $projectContext = "Informasi Proyek:\n"
                ."- Nama Proyek: {$project->name}\n"
                ."- Masalah: {$project->problem_statement}\n"
                ."- Tujuan: {$project->project_goals}\n"
                ."- Output: {$project->expected_outputs}\n"
                .'- Scope In: '.implode(', ', (array) ($project->scope_included ?? []))."\n"
                .'- Scope Out: '.implode(', ', (array) ($project->scope_excluded ?? []))."\n"
                ."- Kategori: {$project->category}\n"
                ."- Tahap Timeline Saat Ini: {$project->current_stage}\n"
                ."- Instruksi Tambahan User: {$promptText}";

            $res = $aiService->chat([
                'system' => $systemInstruction,
                'message' => $projectContext,
                'temperature' => 0.7,
            ]);

            // Parse response
            $cleanJson = trim($res->content);
            if (strpos($cleanJson, '```json') !== false) {
                $cleanJson = str_replace(['```json', '```'], '', $cleanJson);
                $cleanJson = trim($cleanJson);
            }

            $data = json_decode($cleanJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format from AI response: '.$res->content);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal merancang portofolio: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate atau regenerasi token share klien
     */
    public function generateShareToken(Project $project): RedirectResponse
    {
        $project->share_token = Str::random(32);
        $project->save();

        return back()->with('success', 'Link share klien berhasil dibuat/diperbarui.');
    }

    /**
     * Nonaktifkan link share klien
     */
    public function disableShareToken(Project $project): RedirectResponse
    {
        $project->share_token = null;
        $project->save();

        return back()->with('success', 'Link share klien berhasil dinonaktifkan.');
    }

    /**
     * Jawab pertanyaan dari klien
     */
    public function answerQuestion(Request $request, $questionId): RedirectResponse
    {
        $request->validate([
            'answer' => 'required|string|max:1000',
        ]);

        DB::table('project_client_questions')
            ->where('id', $questionId)
            ->update([
                'answer' => trim($request->answer),
                'answered_by' => Auth::id(),
                'answered_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        return back()->with('success', 'Pertanyaan klien berhasil dijawab.');
    }

    /**
     * Arsipkan atau aktifkan kembali proyek
     */
    public function toggleArchive(Project $project): RedirectResponse
    {
        $myCompanyIds = CompanyProfile::where('manager_id', Auth::id())->pluck('id');
        if ($project->created_by !== Auth::id() && ! $myCompanyIds->contains($project->company_profile_id)) {
            abort(403, 'Akses Ditolak: Anda bukan pemilik atau pengelola proyek ini.');
        }

        $project->update([
            'is_archived' => ! $project->is_archived,
        ]);

        $message = $project->is_archived
            ? 'Project berhasil diarsipkan (disembunyikan dari daftar aktif).'
            : 'Project berhasil diaktifkan kembali.';

        return back()->with('success', $message);
    }
}
