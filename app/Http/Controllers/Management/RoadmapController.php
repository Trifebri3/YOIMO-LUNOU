<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectRoadmap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoadmapController extends Controller
{
    public function index(Project $project): View
    {
        $project->load(['roadmaps', 'company']);
        return view('management.projects.roadmaps.index', compact('project'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
            'status'       => ['required', 'in:Pending,In Progress,Completed'],
            'objectives'   => ['nullable', 'array'],
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        // 1. Validasi Batas Tanggal Project (Tidak boleh sebelum start_date atau lewat deadline project)
        if ($project->start_date && $start < $project->start_date->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'start_date' => 'Tanggal mulai linimasa tidak boleh mendahului tanggal mulai project (' . $project->start_date->format('d M Y') . ').'
            ]);
        }

        if ($project->deadline && $end > $project->deadline->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'end_date' => 'Tanggal selesai linimasa tidak boleh melebihi deadline akhir project (' . $project->deadline->format('d M Y') . ').'
            ]);
        }

        // 2. Validasi Anti-Tumpang Tindih (No Overlapping with Existing Roadmaps)
        $overlap = ProjectRoadmap::where('project_id', $project->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_date' => 'Jadwal linimasa bentrok / tumpang tindih dengan fase linimasa lain yang sudah ada di proyek ini.'
            ]);
        }

        // Siapkan Target Ketercapaian
        $objectives = [];
        if ($request->filled('objectives')) {
            foreach ($request->objectives as $target) {
                if (!empty(trim($target))) {
                    $objectives[] = [
                        'target'      => trim($target),
                        'is_achieved' => false
                    ];
                }
            }
        }

        ProjectRoadmap::create([
            'project_id'          => $project->id,
            'title'               => $request->title,
            'description'         => $request->description,
            'start_date'          => $start,
            'end_date'            => $end,
            'status'              => $request->status,
            'progress_percentage' => 0,
            'objectives'          => $objectives,
        ]);

        return redirect()->route('management.projects.roadmaps.index', $project->id)
            ->with('success', 'Fase linimasa / roadmap baru berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectRoadmap $roadmap): RedirectResponse
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
            'status'       => ['required', 'in:Pending,In Progress,Completed'],
            'objectives'   => ['nullable', 'array'],
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        // Validasi Rentang Project
        if ($project->start_date && $start < $project->start_date->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'start_date' => 'Tanggal mulai linimasa tidak boleh mendahului tanggal mulai project (' . $project->start_date->format('d M Y') . ').'
            ]);
        }

        if ($project->deadline && $end > $project->deadline->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'end_date' => 'Tanggal selesai linimasa tidak boleh melebihi deadline project (' . $project->deadline->format('d M Y') . ').'
            ]);
        }

        // Validasi Overlapping (Kecualikan ID roadmap saat ini)
        $overlap = ProjectRoadmap::where('project_id', $project->id)
            ->where('id', '!=', $roadmap->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_date' => 'Jadwal linimasa bentrok / tumpang tindih dengan fase linimasa lain yang sudah ada.'
            ]);
        }

        // Proses Target & Status Ketercapaian
        $objectives = [];
        $achievedCount = 0;
        if ($request->filled('objectives')) {
            $achievedInputs = $request->input('achieved_status', []);
            foreach ($request->objectives as $idx => $targetText) {
                if (!empty(trim($targetText))) {
                    $isAchieved = isset($achievedInputs[$idx]) && $achievedInputs[$idx] == '1';
                    if ($isAchieved) $achievedCount++;
                    
                    $objectives[] = [
                        'target'      => trim($targetText),
                        'is_achieved' => $isAchieved
                    ];
                }
            }
        }

        // Hitung persentase ketercapaian otomatis
        $totalObjectives = count($objectives);
        $progress = $totalObjectives > 0 ? round(($achievedCount / $totalObjectives) * 100) : 0;

        $roadmap->update([
            'title'               => $request->title,
            'description'         => $request->description,
            'start_date'          => $start,
            'end_date'            => $end,
            'status'              => $request->status,
            'progress_percentage' => $progress,
            'objectives'          => $objectives,
        ]);

        return redirect()->route('management.projects.roadmaps.index', $project->id)
            ->with('success', 'Fase linimasa & target ketercapaian berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectRoadmap $roadmap): RedirectResponse
    {
        $roadmap->delete();
        return redirect()->route('management.projects.roadmaps.index', $project->id)
            ->with('success', 'Fase linimasa telah dihapus.');
    }

    public function generateRoadmapAi(Project $project): RedirectResponse
    {
        // 1. Get Project Context
        $projectName = $project->name;
        $projectBrief = $project->description ?: 'Tidak ada deskripsi detail.';
        $problemStatement = $project->problem_statement ?: '-';
        $goals = $project->goals ?: '-';
        $targetOutput = $project->target_output ?: '-';
        $startDate = $project->start_date ? $project->start_date->format('Y-m-d') : now()->format('Y-m-d');
        $deadline = $project->deadline ? $project->deadline->format('Y-m-d') : now()->addMonths(3)->format('Y-m-d');

        // Calculate total days
        $start = new \DateTime($startDate);
        $end = new \DateTime($deadline);
        $totalDays = $end->diff($start)->days;
        if ($totalDays <= 0) {
            $totalDays = 90; // Default fallback to 90 days
        }

        // 2. Query Gemini/OpenRouter to generate sequential phases
        $systemPrompt = "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Anda menguasai manajemen proyek berbasis SDLC (Software Development Life Cycle), metodologi Agile/Scrum, manajemen risiko, serta penentuan KPI (Key Performance Indicators) teknologi yang presisi. Dalam menyusun roadmap, Anda wajib mengadopsi pola pikir Systems Thinking (melihat proyek sebagai kesatuan sistem yang saling terhubung), Design Thinking (berorientasi pada pemecahan masalah secara kreatif, empati pengguna, ideasi, dan pengujian berulang), serta Human-First (mengutamakan kebutuhan manusia/pengguna akhir, kenyamanan & kejelasan bagi pengembang, serta dampak positif aplikasi bagi penggunanya).\n\n"
            . "Tugas Anda adalah membagi linimasa proyek menjadi 3-5 fase pengerjaan berurutan secara logis (Roadmap/Timeline) dan mengembalikan hasilnya dalam format JSON murni.\n\n"
            . "DETAIL PROYEK:\n"
            . "- Nama Proyek: {$projectName}\n"
            . "- Deskripsi/Brief: {$projectBrief}\n"
            . "- Masalah: {$problemStatement}\n"
            . "- Tujuan: {$goals}\n"
            . "- Target/Output: {$targetOutput}\n"
            . "- Tanggal Mulai Proyek: {$startDate}\n"
            . "- Batas Akhir/Deadline Proyek: {$deadline}\n"
            . "- Durasi Total Proyek: {$totalDays} hari\n\n"
            . "ATURAN TANGGAL & HILIR-MUDIK JADWAL:\n"
            . "1. Seluruh fase harus berurutan secara kronologis (tidak boleh tumpang tindih!).\n"
            . "2. Fase 1 harus dimulai pada tanggal {$startDate}.\n"
            . "3. Fase berikutnya harus dimulai minimal 1 hari setelah tanggal selesai fase sebelumnya.\n"
            . "4. Fase terakhir harus selesai paling lambat pada tanggal {$deadline}.\n"
            . "5. Bagilah durasi total ({$totalDays} hari) secara proporsional dan logis di antara 3 hingga 5 fase tersebut.\n\n"
            . "ATURAN STRUKTUR ROADMAP & KPI IT PROJECT MANAGEMENT:\n"
            . "1. Susunlah fase pengerjaan mengikuti standar SDLC yang relevan (Inisiasi & Riset Kebutuhan, Desain UI/UX & Arsitektur, Pengembangan Modul Inti & API, Quality Assurance & Security Testing, Deployment & User Training).\n"
            . "2. Target ketercapaian (Objectives) harus dibuat sangat DETAIL, GRANULAR, dan dipecah menjadi hal-hal kecil (minimal 5 hingga 10 target per fase).\n"
            . "3. Setiap target wajib bersifat KUANTITATIF dan memiliki KPI terukur dengan angka yang jelas (contoh: 'Menyusun 1 BRD (Business Requirement Document)', 'Membuat 15 screen mockups di Figma', 'Mengembangkan 4 API endpoint dengan response time < 200ms', 'Mendapatkan 100% skor kelulusan QA pada 20 test cases', 'Melakukan 1 kali training untuk 10 calon user').\n\n"
            . "FORMAT JSON YANG DIHARAPKAN:\n"
            . "[\n"
            . "  {\n"
            . "    \"title\": \"Fase 1: Judul Fase\",\n"
            . "    \"description\": \"Deskripsi singkat fase...\",\n"
            . "    \"start_date\": \"YYYY-MM-DD\",\n"
            . "    \"end_date\": \"YYYY-MM-DD\",\n"
            . "    \"objectives\": [\"Target Kuantitatif 1\", \"Target Kuantitatif 2\"]\n"
            . "  },\n"
            . "  ...\n"
            . "]\n\n"
            . "Kembalikan HANYA array JSON murni tersebut tanpa penjelasan markdown, pembungkus ```json, atau teks lainnya.";

        try {
            $aiService = app(\App\Services\AIService::class);
            $response = $aiService->chat([
                'system' => "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Anda merancang linimasa dengan pola pikir Systems Thinking, Design Thinking, dan Human-First. Tugas Anda adalah membagi linimasa proyek menjadi 3-5 fase pengerjaan berurutan secara logis (Roadmap/Timeline) menggunakan standar manajemen proyek IT/SDLC, dengan target ketercapaian yang sangat detail, granular, dan kuantitatif (memiliki angka terukur/KPI yang jelas, minimal 5-10 target per fase), serta mengembalikan hasilnya dalam format JSON murni.",
                'message' => "DETAIL PROYEK:\n"
                    . "- Nama Proyek: {$projectName}\n"
                    . "- Deskripsi/Brief: {$projectBrief}\n"
                    . "- Masalah: {$problemStatement}\n"
                    . "- Tujuan: {$goals}\n"
                    . "- Target/Output: {$targetOutput}\n"
                    . "- Tanggal Mulai Proyek: {$startDate}\n"
                    . "- Batas Akhir/Deadline Proyek: {$deadline}\n"
                    . "- Durasi Total Proyek: {$totalDays} hari\n\n"
                    . "ATURAN TANGGAL & HILIR-MUDIK JADWAL:\n"
                    . "1. Seluruh fase harus berurutan secara kronologis (tidak boleh tumpang tindih!).\n"
                    . "2. Fase 1 harus dimulai pada tanggal {$startDate}.\n"
                    . "3. Fase berikutnya harus dimulai minimal 1 hari setelah tanggal selesai fase sebelumnya.\n"
                    . "4. Fase terakhir harus selesai paling lambat pada tanggal {$deadline}.\n"
                    . "5. Bagilah durasi total ({$totalDays} hari) secara proporsional dan logis di antara 3 hingga 5 fase tersebut.\n\n"
                    . "FORMAT JSON YANG DIHARAPKAN:\n"
                    . "[\n"
                    . "  {\n"
                    . "    \"title\": \"Fase 1: Judul Fase\",\n"
                    . "    \"description\": \"Deskripsi singkat fase...\",\n"
                    . "    \"start_date\": \"YYYY-MM-DD\",\n"
                    . "    \"end_date\": \"YYYY-MM-DD\",\n"
                    . "    \"objectives\": [\"Target 1\", \"Target 2\"]\n"
                    . "  },\n"
                    . "  ...\n"
                    . "]\n\n"
                    . "Kembalikan HANYA array JSON murni tersebut tanpa penjelasan markdown, pembungkus ```json, atau teks lainnya.",
                'temperature' => 0.2
            ]);

            $content = $response->content;

            // Clean markdown block wrappers if present
            $content = trim($content);
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
            }
            $content = trim($content);

            $phases = json_decode($content, true);

            if (!is_array($phases)) {
                throw new \Exception("Gagal melakukan parsing JSON dari AI: " . $content);
            }

            // Delete existing roadmaps before generating new ones to prevent database overlap/conflicts
            ProjectRoadmap::where('project_id', $project->id)->delete();

            foreach ($phases as $phase) {
                $objectives = [];
                if (!empty($phase['objectives'])) {
                    foreach ($phase['objectives'] as $target) {
                        $objectives[] = [
                            'target'      => trim($target),
                            'is_achieved' => false
                        ];
                    }
                }

                ProjectRoadmap::create([
                    'project_id'          => $project->id,
                    'title'               => $phase['title'],
                    'description'         => $phase['description'] ?? '',
                    'start_date'          => $phase['start_date'],
                    'end_date'            => $phase['end_date'],
                    'status'              => 'Pending',
                    'progress_percentage' => 0,
                    'objectives'          => $objectives,
                ]);
            }

            // Catat Audit Log
            \App\Models\ProjectActivityLog::record($project->id, 'Roadmap', 'CREATE', "Men-generate seluruh alur linimasa proyek secara otomatis menggunakan LUNOU AI.");

            return redirect()->route('management.projects.roadmaps.index', $project->id)
                ->with('success', 'Alur linimasa proyek berhasil di-generate secara otomatis menggunakan LUNOU AI.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to generate Roadmap via AI: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembuatan linimasa otomatis: ' . $e->getMessage());
        }
    }

    public function suggestObjectivesAi(Request $request, Project $project): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title'       => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $phaseTitle = $request->title;
        $phaseDesc = $request->description ?: 'Tidak ada deskripsi.';
        $projectName = $project->name;
        $projectBrief = $project->description ?: 'Tidak ada deskripsi detail.';
        $goals = $project->goals ?: '-';

        $systemPrompt = "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Dalam merancang target, Anda wajib menerapkan pola pikir Systems Thinking, Design Thinking, dan Human-First. Tugas Anda adalah memberikan rekomendasi target ketercapaian (objectives/deliverables) untuk fase proyek tertentu.\n\n"
            . "DETAIL PROYEK:\n"
            . "- Nama Proyek: {$projectName}\n"
            . "- Deskripsi/Brief Proyek: {$projectBrief}\n"
            . "- Tujuan Proyek: {$goals}\n\n"
            . "DETAIL FASE:\n"
            . "- Judul Fase: {$phaseTitle}\n"
            . "- Deskripsi/Ruang Lingkup Fase: {$phaseDesc}\n\n"
            . "ATURAN TARGET KETERCAPAIAN (OBJECTIVES):\n"
            . "1. Rekomendasikan minimal 5 hingga 8 target ketercapaian yang sangat DETAIL, GRANULAR, dan dipecah ke hal-hal kecil.\n"
            . "2. Setiap target wajib bersifat KUANTITATIF dengan metrik/angka terukur yang jelas (contoh: 'Menyusun 1 dokumen spesifikasi', 'Membuat 10 mockups screen', 'Mengembangkan 3 fitur API dengan response time < 200ms', 'Melakukan 1 sesi UAT').\n"
            . "3. Kembalikan hasilnya dalam format JSON murni berupa array string, contoh: [\"Target 1\", \"Target 2\", ...]. Jangan berikan markdown, pembungkus ```json, atau penjelasan apapun.";

        try {
            $aiService = app(\App\Services\AIService::class);
            $response = $aiService->chat([
                'system' => "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager. Anda menerapkan pola pikir Systems Thinking, Design Thinking, dan Human-First untuk menyarankan target ketercapaian kuantitatif.",
                'message' => $systemPrompt,
                'temperature' => 0.2
            ]);

            $content = trim($response->content);
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
            }
            $content = trim($content);

            $objectives = json_decode($content, true);

            if (!is_array($objectives)) {
                throw new \Exception("Format respon AI tidak valid: " . $content);
            }

            return response()->json([
                'status'     => 'success',
                'objectives' => $objectives
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function enhanceRoadmapAi(Request $request, Project $project, ProjectRoadmap $roadmap): RedirectResponse
    {
        $projectName = $project->name;
        $projectBrief = $project->description ?: 'Tidak ada deskripsi detail.';
        $goals = $project->goals ?: '-';
        $customInstruction = $request->input('instruction') ?: 'Tidak ada instruksi khusus.';

        $systemPrompt = "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Dalam menyempurnakan rincian fase, Anda wajib mengadopsi pola pikir Systems Thinking (melihat proyek sebagai kesatuan sistem), Design Thinking (kreatif, empati pengguna, ideasi), dan Human-First (mengutamakan kebutuhan manusia/pengguna akhir). Tugas Anda adalah menyempurnakan rincian dari satu fase linimasa proyek (Roadmap Phase) agar lebih detail, granular, dan kuantitatif.\n\n"
            . "DETAIL PROYEK:\n"
            . "- Nama Proyek: {$projectName}\n"
            . "- Deskripsi/Brief Proyek: {$projectBrief}\n"
            . "- Tujuan Proyek: {$goals}\n\n"
            . "FASE SAAT INI:\n"
            . "- Judul Fase: {$roadmap->title}\n"
            . "- Deskripsi Fase: " . ($roadmap->description ?: 'Belum ada.') . "\n\n"
            . "INSTRUKSI KHUSUS DARI USER (IKUTI PERINTAH INI DENGAN PRIORITAS TINGGI):\n"
            . "{$customInstruction}\n\n"
            . "ATURAN PENYEMPURNAAN:\n"
            . "1. Tulis ulang deskripsi fase agar menggambarkan ruang lingkup pengerjaan secara profesional dan jelas (buat tetap ringkas, sekitar 2-3 kalimat) dengan mempertimbangkan instruksi khusus di atas.\n"
            . "2. Buat minimal 5-8 target ketercapaian (objectives) yang sangat DETAIL, GRANULAR, dan KUANTITATIF (wajib ada angka konkret/metrik terukur) dengan mempertimbangkan instruksi khusus di atas.\n"
            . "3. Kembalikan hasilnya dalam format JSON murni:\n"
            . "{\n"
            . "  \"description\": \"Deskripsi baru...\",\n"
            . "  \"objectives\": [\"Target Kuantitatif 1\", \"Target Kuantitatif 2\", ...]\n"
            . "}\n"
            . "Jangan berikan penjelasan markdown, pembungkus ```json, atau tulisan lain selain objek JSON tersebut.";

        try {
            $aiService = app(\App\Services\AIService::class);
            $response = $aiService->chat([
                'system' => "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager. Anda menerapkan pola pikir Systems Thinking, Design Thinking, dan Human-First untuk memperbarui deskripsi dan target kuantitatif.",
                'message' => $systemPrompt,
                'temperature' => 0.2
            ]);

            $content = trim($response->content);
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
            }
            $content = trim($content);

            $result = json_decode($content, true);

            if (!is_array($result) || !isset($result['objectives'])) {
                throw new \Exception("Format respon AI tidak valid: " . $content);
            }

            $objectives = [];
            foreach ($result['objectives'] as $target) {
                $objectives[] = [
                    'target'      => trim($target),
                    'is_achieved' => false
                ];
            }

            $roadmap->update([
                'description' => $result['description'] ?? $roadmap->description,
                'objectives'  => $objectives
            ]);

            // Catat Audit Log
            \App\Models\ProjectActivityLog::record($project->id, 'Roadmap', 'UPDATE', "Menyempurnakan fase linimasa [{$roadmap->title}] secara otomatis menggunakan LUNOU AI.");

            return redirect()->route('management.projects.roadmaps.index', $project->id)
                ->with('success', 'Fase linimasa "' . $roadmap->title . '" berhasil disempurnakan menggunakan LUNOU AI.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to enhance Roadmap Phase via AI: " . $e->getMessage());
            return back()->with('error', 'Gagal menyempurnakan fase menggunakan AI: ' . $e->getMessage());
        }
    }
}