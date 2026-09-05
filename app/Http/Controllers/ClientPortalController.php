<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\AIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClientPortalController extends Controller
{
    /**
     * Tampilkan Portal Klien Publik
     */
    public function show($token)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        // 1. Get Roadmap Phases
        $roadmaps = DB::table('project_roadmaps')
            ->where('project_id', $project->id)
            ->orderBy('start_date')
            ->get();

        // 2. Get Tasks
        $tasks = DB::table('project_tasks')
            ->where('project_id', $project->id)
            ->orderBy('due_date')
            ->get();

        // 3. Calculate Progress
        $totalTasks = count($tasks);
        $completedTasks = $tasks->where('status', 'Completed')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // 4. Get Client Questions
        $questions = DB::table('project_client_questions')
            ->where('project_id', $project->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Get AI Executive Project Report (Cached for 1 hour)
        $aiReport = Cache::remember('client_project_report_'.$project->id, 3600, function () use ($project, $roadmaps, $tasks) {
            return $this->generateProjectAiReport($project, $roadmaps, $tasks);
        });

        return view('client.portal', compact('project', 'roadmaps', 'tasks', 'progress', 'questions', 'aiReport'));
    }

    /**
     * Kirim Pertanyaan Klien
     */
    public function submitQuestion(Request $request, $token)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        $request->validate([
            'client_name' => 'required|string|max:100',
            'question' => 'required|string|max:1000',
        ]);

        DB::table('project_client_questions')->insert([
            'project_id' => $project->id,
            'client_name' => trim($request->client_name),
            'question' => trim($request->question),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Pertanyaan Anda berhasil dikirim ke tim proyek.');
    }

    /**
     * Generate Project Executive Report using LUNOU AI
     */
    private function generateProjectAiReport($project, $roadmaps, $tasks)
    {
        $aiService = app(AIService::class);

        // Format parameters
        $roadmapStr = '';
        foreach ($roadmaps as $idx => $r) {
            $roadmapStr .= '- Fase '.($idx + 1).": {$r->title} ({$r->start_date} s/d {$r->end_date})\n";
        }

        $tasksStr = '';
        foreach ($tasks->take(20) as $t) {
            $tasksStr .= "- [{$t->status}] {$t->title} (PIC: ".($t->assigned_to ? 'Ada' : 'Belum Ada').")\n";
        }

        $prompt = "Anda adalah LUNOU AI, asisten manajemen proyek profesional.
Tolong buatkan ringkasan eksekutif laporan kemajuan proyek (executive summary project report) yang rapi, profesional, santun, dan menenangkan untuk dibaca oleh KLIEN perusahaan kami.

INFORMASI PROYEK:
Nama Proyek: {$project->name}
Deskripsi: {$project->description}

ROADMAP LINIMASA:
{$roadmapStr}

TUGAS AKTIF & STATUS:
{$tasksStr}

ATURAN OUTPUT:
1. Tulis laporan dalam Bahasa Indonesia yang formal namun ramah dan hangat.
2. Jangan menggunakan emotikon atau emoji sama sekali.
3. Fokus pada progres kerja saat ini, fase terdekat yang sedang berjalan, dan kesiapan tim.
4. Buat dalam format HTML ringkas (gunakan paragraf <p> dan poin-poin <ul>/<li> jika diperlukan). Jangan menyertakan tag html/body lengkap, cukup struktur kontennya saja.";

        try {
            $response = $aiService->chat([
                ['role' => 'user', 'content' => $prompt],
            ]);

            return $response['choices'][0]['message']['content'] ?? '<p>Gagal memuat laporan otomatis dari LUNOU AI.</p>';
        } catch (\Exception $e) {
            return '<p>Laporan kemajuan proyek saat ini sedang dalam proses penyusunan.</p>';
        }
    }
}
