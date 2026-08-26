<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAgenda;
use App\Models\ProjectDocument;
use App\Models\ProjectTask;
use App\Models\WellbeingCheckin;
use App\Models\WellbeingJournal;
use App\Models\WellbeingGoal;
use App\Models\WellbeingReflection;
use App\Models\WellbeingLeftThought;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // Cek login harian & berikan poin
        \App\Services\GamificationService::checkDailyLogin($userId);

        // 1. Ambil Proyek yang melibatkan user
        $myProjects = Project::whereJsonContains('team_matrix', ['user_id' => (string) $userId])
            ->orWhereJsonContains('team_matrix', ['user_id' => (int) $userId])
            ->orWhere('created_by', $userId)
            ->with(['roadmaps'])
            ->latest()
            ->get();

        $projectIds = $myProjects->pluck('id')->toArray();

        // 2. Query Seluruh Tugas Personal User
        $tasksQuery = ProjectTask::where('assigned_to', $userId)
            ->with(['project', 'roadmap']);

        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        $myTasks = $tasksQuery->orderByRaw("FIELD(status, 'In Progress', 'Todo', 'Review', 'Completed')")
            ->orderBy('due_date', 'asc')
            ->get();

        // 3. Metrik Statistik Tugas Personal
        $totalAssigned = ProjectTask::where('assigned_to', $userId)->count();
        $inProgressCount = ProjectTask::where('assigned_to', $userId)->where('status', 'In Progress')->count();
        $todoCount = ProjectTask::where('assigned_to', $userId)->where('status', 'Todo')->count();
        $reviewCount = ProjectTask::where('assigned_to', $userId)->where('status', 'Review')->count();
        $completedCount = ProjectTask::where('assigned_to', $userId)->where('status', 'Completed')->count();
        
        $completionRate = $totalAssigned > 0 ? round(($completedCount / $totalAssigned) * 100) : 0;

        // 4. Deadlines Radar (Tugas yang mendekati tenggat waktu dalam 7 hari ke depan)
        $upcomingDeadlines = ProjectTask::where('assigned_to', $userId)
            ->whereIn('status', ['Todo', 'In Progress'])
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // 5. Agenda & Acara Tim yang Mengundang User Ini
        $myAgendas = ProjectAgenda::whereIn('project_id', $projectIds)
            ->where(function ($query) use ($userId) {
                $query->whereJsonContains('attendee_ids', (string) $userId)
                      ->orWhereJsonContains('attendee_ids', (int) $userId)
                      ->orWhere('created_by', $userId);
            })
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        // 6. Dokumen Wajib Baca yang Belum Dibuka oleh User
        $mandatoryDocs = ProjectDocument::whereIn('project_id', $projectIds)
            ->where('is_mandatory', true)
            ->get()
            ->filter(function ($doc) use ($userId) {
                $readers = $doc->readers_log ?? [];
                return !collect($readers)->contains('user_id', $userId);
            });

        // 7. Tugas Terbuka (Open Pool Tasks) yang bisa diklaim oleh user di proyeknya
        $openTasks = ProjectTask::whereIn('project_id', $projectIds)
            ->whereNull('assigned_to')
            ->where('status', '!=', 'Completed')
            ->with('project')
            ->take(4)
            ->get();

        // 8. Wellbeing Workspace Data
        $todayCheckin = WellbeingCheckin::where('user_id', $userId)->whereDate('checkin_date', now()->toDateString())->first();
        $personalGoals = WellbeingGoal::where('user_id', $userId)->latest()->get();
        $latestJournal = WellbeingJournal::where('user_id', $userId)->latest()->first();
        $myJournals = WellbeingJournal::where('user_id', $userId)->latest()->get();
        
        $currentWeekStr = now()->format('Y-\WW'); // e.g. 2026-W34
        $weeklyReflection = WellbeingReflection::where('user_id', $userId)->where('week_number', $currentWeekStr)->first();

        // Workload & Burnout Calculator
        $activeTasksCount = ProjectTask::where('assigned_to', $userId)->where('status', '!=', 'Completed')->count();
        $urgentTasksCount = ProjectTask::where('assigned_to', $userId)->where('status', '!=', 'Completed')->where('priority', 'Urgent')->count();
        $overdueTasksCount = ProjectTask::where('assigned_to', $userId)->where('status', '!=', 'Completed')
            ->whereNotNull('due_date')->whereDate('due_date', '<', now()->toDateString())->count();

        $workloadAdvice = "Beban pekerjaanmu terpantau stabil minggu ini. Tetap jaga ritme kerja yang seimbang ya!";
        $workloadSeverity = "low"; // low, medium, high

        if ($activeTasksCount > 5) {
            $workloadAdvice = "Beban pekerjaanmu cukup tinggi minggu ini ({$activeTasksCount} tugas aktif). Mungkin waktunya menyelesaikan prioritas utama sebelum mengambil pekerjaan baru.";
            $workloadSeverity = "high";
        } elseif ($urgentTasksCount > 1) {
            $workloadAdvice = "Ada {$urgentTasksCount} tugas mendesak (Urgent) yang menunggumu. Jangan ragu untuk mendiskusikan prioritas jika dirasa terlalu menekan.";
            $workloadSeverity = "high";
        } elseif ($overdueTasksCount > 0) {
            $workloadAdvice = "Terdapat {$overdueTasksCount} tugas yang melewati deadline. Tarik napas dalam, selesaikan satu per satu, kesehatan mentalmu lebih berharga.";
            $workloadSeverity = "medium";
        }

        // 9. Gamification Workspace Data
        $userPoint = \App\Models\UserPoint::firstOrCreate(
            ['user_id' => $userId],
            ['total_points' => 0, 'level' => 1, 'login_streak' => 0]
        );

        $awards = \App\Models\UserAward::where('user_id', $userId)->latest()->get();

        $pointLogs = \App\Models\UserPointLog::where('user_id', $userId)
            ->with(['company', 'project'])
            ->latest()
            ->take(15)
            ->get();

        // Akumulasi per company
        $companyPoints = \App\Models\UserPointLog::where('user_id', $userId)
            ->whereNotNull('company_profile_id')
            ->select('company_profile_id', \Illuminate\Support\Facades\DB::raw('SUM(points) as total'))
            ->groupBy('company_profile_id')
            ->with('company')
            ->get();

        // Akumulasi per project
        $projectPoints = \App\Models\UserPointLog::where('user_id', $userId)
            ->whereNotNull('project_id')
            ->select('project_id', \Illuminate\Support\Facades\DB::raw('SUM(points) as total'))
            ->groupBy('project_id')
            ->with('project')
            ->get();

        // Dynamic AI Work Motivation Analysis based on user stats
        $workMotivationText = \Illuminate\Support\Facades\Cache::remember("work_motivation_user_" . $userId, 1800, function() use ($completedCount, $totalAssigned, $userPoint, $todayCheckin) {
            try {
                $aiService = app(\App\Services\AIService::class);
                $prompt = "Kamu adalah LUNOU, asisten wellness & produktivitas. Berikan analisis motivasi kerja singkat yang sangat membakar semangat, hangat, dan empati untuk pengguna.\n\n"
                    . "Data pengguna saat ini:\n"
                    . "- Tugas selesai: {$completedCount} dari {$totalAssigned} tugas\n"
                    . "- Level XP: Level {$userPoint->level} ({$userPoint->total_points} XP)\n"
                    . "- Mood hari ini: " . ($todayCheckin ? $todayCheckin->mood : 'Belum check-in') . "\n\n"
                    . "Buatlah analisis ringkas (2-3 kalimat) yang mengapresiasi kerja kerasnya, memotivasi dia untuk tetap semangat bekerja hari ini, dan memberikan 1 tips produktivitas kecil. Gunakan Bahasa Indonesia yang ramah dan suportif.";

                $res = $aiService->chat([
                    'system' => 'Asisten motivasi kerja yang membakar semangat.',
                    'message' => $prompt,
                    'temperature' => 0.8
                ]);
                return $res->content;
            } catch (\Exception $e) {
                return "Setiap langkah kecil yang kamu ambil hari ini membawa kemajuan besar untuk proyekmu. Tetap fokus, jaga kesehatan mentalmu, dan mari selesaikan hari ini dengan bangga!";
            }
        });

        return view('user.dashboard', compact(
            'myProjects',
            'myTasks',
            'totalAssigned',
            'inProgressCount',
            'todoCount',
            'reviewCount',
            'completedCount',
            'completionRate',
            'upcomingDeadlines',
            'myAgendas',
            'mandatoryDocs',
            'openTasks',
            'todayCheckin',
            'personalGoals',
            'latestJournal',
            'myJournals',
            'weeklyReflection',
            'workloadAdvice',
            'workloadSeverity',
            'userPoint',
            'awards',
            'pointLogs',
            'companyPoints',
            'projectPoints',
            'workMotivationText'
        ));
    }

    /**
     * Simpan mood harian (Daily Check-in)
     */
    public function checkin(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $request->validate([
            'mood'           => ['required', 'string'],
            'energy'         => ['required', 'integer', 'between:1,5'],
            'mental_load'    => ['required', 'integer', 'between:1,5'],
            'rest_condition' => ['required', 'integer', 'between:1,5'],
            'thoughts'       => ['nullable', 'string', 'max:2000'],
        ]);

        WellbeingCheckin::updateOrCreate(
            ['user_id' => $userId, 'checkin_date' => now()->toDateString()],
            $request->only(['mood', 'energy', 'mental_load', 'rest_condition', 'thoughts'])
        );

        return redirect()->back()->with('success', 'Daily Check-in berhasil disimpan. Terima kasih sudah jujur pada dirimu sendiri hari ini!');
    }

    /**
     * Simpan jurnal pribadi (Personal Journal)
     */
    public function storeJournal(Request $request): RedirectResponse
    {
        $request->validate([
            'feeling'     => ['nullable', 'string', 'max:5000'],
            'today_event' => ['nullable', 'string', 'max:5000'],
            'gratitude'   => ['nullable', 'string', 'max:5000'],
            'let_go'      => ['nullable', 'string', 'max:5000'],
            'improvement' => ['nullable', 'string', 'max:5000'],
        ]);

        WellbeingJournal::create([
            'user_id'     => Auth::id(),
            'feeling'     => $request->feeling,
            'today_event' => $request->today_event,
            'gratitude'   => $request->gratitude,
            'let_go'      => $request->let_go,
            'improvement' => $request->improvement,
        ]);

        return redirect()->back()->with('success', 'Jurnal pribadi berhasil disimpan dengan aman.');
    }

    /**
     * Tambah target pribadi (Personal Goals)
     */
    public function storeGoal(Request $request): RedirectResponse
    {
        $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Belajar,Membaca,Olahraga,Istirahat,Family time,Personal project'],
        ]);

        WellbeingGoal::create([
            'user_id'      => Auth::id(),
            'title'        => $request->title,
            'category'     => $request->category,
            'is_completed' => false,
        ]);

        return redirect()->back()->with('success', 'Personal Goal baru berhasil ditambahkan.');
    }

    /**
     * Centang/Selesaikan target pribadi
     */
    public function toggleGoal(WellbeingGoal $goal): RedirectResponse
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->update(['is_completed' => !$goal->is_completed]);

        return redirect()->back()->with('success', 'Status target pribadi berhasil diperbarui.');
    }

    /**
     * Hapus target pribadi
     */
    public function destroyGoal(WellbeingGoal $goal): RedirectResponse
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return redirect()->back()->with('success', 'Target pribadi berhasil dihapus.');
    }

    /**
     * Simpan pikiran sebelum pulang (Leave It Here)
     */
    public function leftThought(Request $request): RedirectResponse
    {
        $request->validate([
            'thought' => ['required', 'string', 'max:5000'],
        ]);

        WellbeingLeftThought::create([
            'user_id' => Auth::id(),
            'thought' => $request->thought,
        ]);

        return redirect()->back()->with('success', 'Pikiranmu telah dilepaskan dan disimpan di sini. Mari pulang dengan tenang. Besok kita mulai lagi.');
    }

    /**
     * Simpan refleksi mingguan (Weekly Reflection)
     */
    public function storeReflection(Request $request): RedirectResponse
    {
        $request->validate([
            'what_went_well'       => ['nullable', 'string', 'max:5000'],
            'what_was_exhausting'  => ['nullable', 'string', 'max:5000'],
            'what_to_change'       => ['nullable', 'string', 'max:5000'],
            'what_proud_of'        => ['nullable', 'string', 'max:5000'],
        ]);

        $userId = Auth::id();
        $currentWeekStr = now()->format('Y-\WW');

        WellbeingReflection::updateOrCreate(
            ['user_id' => $userId, 'week_number' => $currentWeekStr],
            [
                'what_went_well'      => $request->what_went_well,
                'what_was_exhausting' => $request->what_was_exhausting,
                'what_to_change'      => $request->what_to_change,
                'what_proud_of'       => $request->what_proud_of,
                'summary'             => "Minggu ini, hal yang berjalan baik adalah: " . ($request->what_went_well ?? '-') 
                                         . ". Dan hal yang cukup melelahkan adalah: " . ($request->what_was_exhausting ?? '-') 
                                         . ". Namun, hal yang dibanggakan: " . ($request->what_proud_of ?? '-') . "."
            ]
        );

        return redirect()->back()->with('success', 'Weekly Reflection berhasil disimpan. Kamu luar biasa minggu ini!');
    }
    /**
     * Hitung Laporan Keseimbangan Kerja & Jiwa (AI Balance Report)
     */
    public function balanceReport(Request $request): JsonResponse
    {
        $userId = Auth::id();
        
        // 1. Weekly Stats (7 hari terakhir)
        $sevenDaysAgo = now()->subDays(7)->toDateString();
        
        $checkinsWeek = WellbeingCheckin::where('user_id', $userId)
            ->whereDate('checkin_date', '>=', $sevenDaysAgo)
            ->get();
            
        $completedTasksWeek = ProjectTask::where('assigned_to', $userId)
            ->where('status', 'Completed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
            
        $totalTasksWeek = ProjectTask::where('assigned_to', $userId)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $avgEnergyWeek = $checkinsWeek->avg('energy') ?? 3.0;
        $avgMentalWeek = $checkinsWeek->avg('mental_load') ?? 3.0;
        $avgRestWeek = $checkinsWeek->avg('rest_condition') ?? 3.0;
        
        $moodsWeek = $checkinsWeek->pluck('mood')->filter()->toArray();
        $dominantMoodWeek = 'Stabil';
        if (!empty($moodsWeek)) {
            $moodCounts = array_count_values($moodsWeek);
            arsort($moodCounts);
            $dominantMoodWeek = array_key_first($moodCounts);
        }

        // Generate Weekly & Monthly AI Analysis
        $analysisWeek = "Ritme kehidupan kerjamu terpantau stabil minggu ini. Kamu berhasil mengelola emosi dengan baik selagi menyelesaikan tanggung jawab pekerjaan.";
        $analysisMonth = "Dalam sebulan terakhir, koordinasi antara beban mental dan produktivitas kerjamu berjalan selaras secara alami.";
        $recommendations = [];
        $motivation = "Kemajuan kecil yang kamu lakukan hari ini akan berakumulasi menjadi sesuatu yang besar. Hargai ritmemu.";

        try {
            $aiService = app(\App\Services\AIService::class);
            
            // 1. Generate Weekly Analysis
            $weeklyPrompt = "Analisis data kesejahteraan mingguan saya:\n"
                . "- Tugas Selesai: {$completedTasksWeek} dari {$totalTasksWeek}\n"
                . "- Rata-rata Energi: " . round($avgEnergyWeek, 1) . "/5.0\n"
                . "- Beban Mental: " . round($avgMentalWeek, 1) . "/5.0\n"
                . "- Rata-rata Istirahat: " . round($avgRestWeek, 1) . "/5.0\n"
                . "- Mood Dominan: {$dominantMoodWeek}\n"
                . "Berikan 1 paragraf analisis keseimbangan kerja dan emosi yang personal, suportif, dan ringkas (maksimal 2-3 kalimat) dalam bahasa Indonesia.";
                
            $weeklyResponse = $aiService->chat([
                'system' => "Kamu adalah LUNOU, asisten wellbeing cerdas yang ramah.",
                'message' => $weeklyPrompt,
                'temperature' => 0.7,
            ]);
            $analysisWeek = trim($weeklyResponse->content);

            // 2. Generate Monthly Analysis
            // Fetch monthly checkins
            $thirtyDaysAgo = now()->subDays(30)->toDateString();
            $checkinsMonth = WellbeingCheckin::where('user_id', $userId)
                ->whereDate('checkin_date', '>=', $thirtyDaysAgo)
                ->get();
                
            $completedTasksMonth = ProjectTask::where('assigned_to', $userId)
                ->where('status', 'Completed')
                ->where('updated_at', '>=', now()->subDays(30))
                ->count();
                
            $totalTasksMonth = ProjectTask::where('assigned_to', $userId)
                ->where('updated_at', '>=', now()->subDays(30))
                ->count();

            $avgEnergyMonth = $checkinsMonth->avg('energy') ?? 3.0;
            $avgMentalMonth = $checkinsMonth->avg('mental_load') ?? 3.0;
            $avgRestMonth = $checkinsMonth->avg('rest_condition') ?? 3.0;
            
            $moodsMonth = $checkinsMonth->pluck('mood')->filter()->toArray();
            $dominantMoodMonth = 'Stabil';
            if (!empty($moodsMonth)) {
                $moodCounts = array_count_values($moodsMonth);
                arsort($moodCounts);
                $dominantMoodMonth = array_key_first($moodCounts);
            }

            $monthlyPrompt = "Analisis data kesejahteraan bulanan (30 hari terakhir) saya:\n"
                . "- Tugas Selesai: {$completedTasksMonth} dari {$totalTasksMonth}\n"
                . "- Rata-rata Energi: " . round($avgEnergyMonth, 1) . "/5.0\n"
                . "- Beban Mental: " . round($avgMentalMonth, 1) . "/5.0\n"
                . "- Rata-rata Istirahat: " . round($avgRestMonth, 1) . "/5.0\n"
                . "- Mood Dominan: {$dominantMoodMonth}\n"
                . "Berikan 1 paragraf analisis keseimbangan bulanan yang personal, suportif, dan ringkas (maksimal 2-3 kalimat) dalam bahasa Indonesia.";
                
            $monthlyResponse = $aiService->chat([
                'system' => "Kamu adalah LUNOU, asisten wellbeing cerdas yang ramah.",
                'message' => $monthlyPrompt,
                'temperature' => 0.7,
            ]);
            $analysisMonth = trim($monthlyResponse->content);

            // 3. Generate Recommendations
            $recPrompt = "Berdasarkan data di atas (Energi: " . round($avgEnergyWeek, 1) . ", Mental Load: " . round($avgMentalWeek, 1) . ", Istirahat: " . round($avgRestWeek, 1) . "),\n"
                . "Berikan 3 poin rekomendasi tindakan pemulihan yang spesifik dan praktis dalam bahasa Indonesia. Tuliskan langsung sebagai daftar poin teks polos dipisah tanda baris baru (newline) tanpa format markdown (seperti - atau * atau angka).";
                
            $recResponse = $aiService->chat([
                'system' => "Kamu adalah asisten psikolog wellbeing yang ahli.",
                'message' => $recPrompt,
                'temperature' => 0.5,
            ]);
            
            $lines = explode("\n", $recResponse->content);
            foreach ($lines as $line) {
                $line = trim(preg_replace('/^\s*[-*•\d\.]+\s+/', '', $line));
                if (!empty($line)) {
                    $recommendations[] = $line;
                }
            }

            // 4. Generate Motivation Quote
            $motPrompt = "Berikan 1 kalimat kutipan motivasi yang mendalam dan relevan dengan kesehatan mental dan keseimbangan hidup.";
            $motResponse = $aiService->chat([
                'system' => "Kamu adalah motivator wellbeing yang bijak.",
                'message' => $motPrompt,
                'temperature' => 0.8,
            ]);
            $motivation = trim($motResponse->content, ' "');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("AI Wellbeing Report generator fallback triggered: " . $e->getMessage());
            
            // Fallback static jika AI gagal
            if ($avgEnergyWeek < 3.0 && $completedTasksWeek >= 2) {
                $analysisWeek = "LUNOU mendeteksi penyusutan energi. Kamu bekerja keras menyelesaikan {$completedTasksWeek} tugas minggu ini, namun baterai energimu menipis di angka " . round($avgEnergyWeek, 1) . "/5. LUNOU menyarankan ambil jeda relaksasi penuh malam ini.";
            }

            // Fetch monthly checkins for static fallback
            $thirtyDaysAgo = now()->subDays(30)->toDateString();
            $checkinsMonth = WellbeingCheckin::where('user_id', $userId)
                ->whereDate('checkin_date', '>=', $thirtyDaysAgo)
                ->get();
                
            $completedTasksMonth = ProjectTask::where('assigned_to', $userId)
                ->where('status', 'Completed')
                ->where('updated_at', '>=', now()->subDays(30))
                ->count();

            $avgEnergyMonth = $checkinsMonth->avg('energy') ?? 3.0;
            $avgMentalMonth = $checkinsMonth->avg('mental_load') ?? 3.0;
            
            $moodsMonth = $checkinsMonth->pluck('mood')->filter()->toArray();
            $dominantMoodMonth = 'Stabil';
            if (!empty($moodsMonth)) {
                $moodCounts = array_count_values($moodsMonth);
                arsort($moodCounts);
                $dominantMoodMonth = array_key_first($moodCounts);
            }

            if ($avgEnergyMonth < 3.0 && $completedTasksMonth >= 5) {
                $analysisMonth = "Evaluasi Bulanan: Beban kerja kumulatifmu sangat tinggi ({$completedTasksMonth} tugas selesai), tetapi tingkat pemulihan energimu rendah (" . round($avgEnergyMonth, 1) . "/5). Waspada gejala burnout kronis.";
            }

            if (empty($recommendations)) {
                $recommendations[] = "Ambil jeda 5-10 menit setiap 2 jam kerja untuk meluruskan pundak dan minum air putih hangat.";
                $recommendations[] = "Gunakan fitur 'Leave It Here' sebelum pulang. Matikan laptop kantor malam ini.";
            }
        }

        return response()->json([
            'week' => [
                'completed' => $completedTasksWeek,
                'total' => $totalTasksWeek,
                'energy' => round($avgEnergyWeek, 1),
                'mental' => round($avgMentalWeek, 1),
                'rest' => round($avgRestWeek, 1),
                'mood' => $dominantMoodWeek,
                'analysis' => $analysisWeek,
            ],
            'month' => [
                'completed' => $completedTasksMonth ?? 0,
                'total' => $totalTasksMonth ?? 0,
                'energy' => round($avgEnergyMonth ?? 3.0, 1),
                'mental' => round($avgMentalMonth ?? 3.0, 1),
                'rest' => round($avgRestMonth ?? 3.0, 1),
                'mood' => $dominantMoodMonth ?? 'Stabil',
                'analysis' => $analysisMonth,
            ],
            'recommendations' => $recommendations,
            'motivation' => $motivation
        ]);
    }

    /**
     * Diskusi interaktif tentang wellbeing dengan AI
     */
    public function discuss(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $userId = Auth::id();
        $user = Auth::user();

        // Ambil data checkin 7 hari terakhir
        $sevenDaysAgo = now()->subDays(7)->toDateString();
        $checkins = WellbeingCheckin::where('user_id', $userId)
            ->whereDate('checkin_date', '>=', $sevenDaysAgo)
            ->get();
            
        $completedTasks = ProjectTask::where('assigned_to', $userId)
            ->where('status', 'Completed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
            
        $totalTasks = ProjectTask::where('assigned_to', $userId)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $avgEnergy = $checkins->avg('energy') ?? 3.0;
        $avgMental = $checkins->avg('mental_load') ?? 3.0;
        $avgRest = $checkins->avg('rest_condition') ?? 3.0;
        
        $moods = $checkins->pluck('mood')->filter()->toArray();
        $dominantMood = 'Stabil';
        if (!empty($moods)) {
            $moodCounts = array_count_values($moods);
            arsort($moodCounts);
            $dominantMood = array_key_first($moodCounts);
        }

        try {
            $aiService = app(\App\Services\AIService::class);

            $systemPrompt = "Kamu adalah LUNOU, asisten psikolog & wellness coach pribadi yang hangat, berempati, dan solutif di Yoimo Ecosystem. "
                . "Kamu sedang melayani diskusi/konseling interaktif tentang kesejahteraan mental dengan pengguna: {$user->name}. "
                . "Berikut adalah metrik wellbeing 7 hari terakhir miliknya:\n"
                . "- Rata-rata Energi: " . round($avgEnergy, 1) . "/5.0\n"
                . "- Beban Mental: " . round($avgMental, 1) . "/5.0\n"
                . "- Istirahat/Tidur: " . round($avgRest, 1) . "/5.0\n"
                . "- Mood Dominan: {$dominantMood}\n"
                . "- Tugas Proyek Selesai: {$completedTasks} dari {$totalTasks} tugas\n\n"
                . "Jawab pertanyaan pengguna dengan nada bicara yang ramah, hangat, penuh perhatian, dan mendengarkan secara aktif. "
                . "Beri saran praktis, sehat, dan menenangkan. Gunakan bahasa Indonesia yang santai tapi sopan. "
                . "Buat respon berkisar 2-4 kalimat agar mudah dibaca di widget chat.";

            $aiResponse = $aiService->chat([
                'system' => $systemPrompt,
                'message' => $request->message,
                'temperature' => 0.7,
            ]);

            return response()->json([
                'success' => true,
                'reply' => $aiResponse->content,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'LUNOU sedang kesulitan menghubungkan pikiran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Petakan deskripsi perasaan ke nilai check-in harian (Auto Fill AI)
     */
    public function autoFillCheckin(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $aiService = app(\App\Services\AIService::class);

            $systemPrompt = "Tugasmu adalah menganalisis pesan kondisi fisik/mental pengguna hari ini dan memetakan datanya secara akurat ke dalam format JSON.\n\n"
                . "Pilihan mood yang valid hanya: 'Senang', 'Stabil', 'Cemas', 'Lelah', 'Stres'. Pilih salah satu yang paling mendekati deskripsi pengguna.\n"
                . "Skala slider yang digunakan adalah 1 sampai 5:\n"
                . "- energy (Level Energi: 1=Habis/Lemas, 3=Normal/Cukup, 5=Penuh/Segar)\n"
                . "- mental_load (Beban Pikiran: 1=Rendah/Tenang, 3=Sedang, 5=Berat/Penuh Pikiran)\n"
                . "- rest_condition (Kualitas Istirahat: 1=Kurang/Begadang, 3=Cukup, 5=Sangat Baik/Segar)\n\n"
                . "Ekstrak juga ringkasan pikiran pengguna ke bidang 'thoughts' (maksimal 200 karakter).\n\n"
                . "Kamu HARUS merespon HANYA dengan dokumen JSON mentah tanpa format markdown (jangan pakai ```json atau blok kode lainnya). Contoh output:\n"
                . "{\"mood\": \"Senang\", \"energy\": 4, \"mental_load\": 2, \"rest_condition\": 5, \"thoughts\": \"Hari ini merasa sangat produktif dan tidur nyenyak.\"}";

            $aiResponse = $aiService->chat([
                'system' => $systemPrompt,
                'message' => $request->message,
                'temperature' => 0.2,
            ]);

            // Decode response
            $cleanContent = trim($aiResponse->content);
            
            // Remove markdown code blocks if any (in case LLM ignored prompt instructions)
            if (str_starts_with($cleanContent, '```')) {
                $cleanContent = preg_replace('/^```(?:json)?|```$/m', '', $cleanContent);
                $cleanContent = trim($cleanContent);
            }

            $data = json_decode($cleanContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Gagal melakukan parsing JSON dari respon AI: " . $cleanContent);
            }

            return response()->json(array_merge([
                'success' => true,
            ], $data));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'LUNOU gagal menganalisis pesan Anda: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AI Master Auto-Journaler: Menganalisis & memetakan cerita ke Check-in, Jurnal, Target, & Refleksi sekaligus
     */
    public function autoJournal(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $aiService = app(\App\Services\AIService::class);

            $systemPrompt = "Kamu adalah LUNOU, asisten wellness Yoimo yang ahli menganalisis jurnal harian bebas pengguna.\n"
                . "Analisis cerita/pengalaman harian dari pengguna dan petakan datanya ke dalam objek JSON dengan skema berikut:\n\n"
                . "{\n"
                . "  \"checkin\": {\n"
                . "    \"mood\": \"Senang\" | \"Stabil\" | \"Cemas\" | \"Lelah\" | \"Stres\" (pilih salah satu mood paling mendekati),\n"
                . "    \"energy\": 1..5 (skala energi: 1=habis, 3=normal, 5=penuh),\n"
                . "    \"mental_load\": 1..5 (skala beban pikiran: 1=tenang, 3=sedang, 5=berat),\n"
                . "    \"rest_condition\": 1..5 (skala istirahat: 1=kurang tidur/begadang, 3=cukup, 5=sangat baik),\n"
                . "    \"thoughts\": \"Ringkasan pikiran/perasaan hari ini (maks 200 karakter)\"\n"
                . "  },\n"
                . "  \"journal\": {\n"
                . "    \"feeling\": \"Deskripsi perasaan hari ini\",\n"
                . "    \"today_event\": \"Kejadian/aktivitas penting hari ini\",\n"
                . "    \"gratitude\": \"Hal yang disyukuri hari ini (jika ada, jika tidak tulis kosong)\",\n"
                . "    \"let_go\": \"Hal melelahkan yang ingin dilepaskan (jika ada, jika tidak tulis kosong)\",\n"
                . "    \"improvement\": \"Hal yang ingin ditingkatkan esok hari (jika ada, jika tidak tulis kosong)\"\n"
                . "  },\n"
                . "  \"goals\": [\n"
                . "    {\n"
                . "      \"title\": \"Judul target/rencana yang ingin dicapai ke depan\",\n"
                . "      \"category\": \"Belajar\" | \"Membaca\" | \"Olahraga\" | \"Istirahat\" | \"Family time\" | \"Personal project\"\n"
                . "    }\n"
                . "  ] (ekstrak maksimal 3 target baru jika disebutkan, jika tidak ada kirim array kosong),\n"
                . "  \"reflection\": {\n"
                . "    \"what_went_well\": \"Hal yang berjalan dengan baik minggu/hari ini\",\n"
                . "    \"what_was_exhausting\": \"Hal yang melelahkan minggu/hari ini\",\n"
                . "    \"what_to_change\": \"Hal yang ingin diubah agar lebih baik\",\n"
                . "    \"what_proud_of\": \"Pencapaian yang dibanggakan\"\n"
                . "  },\n"
                . "  \"categories\": [\"Pekerjaan\" | \"Kesehatan\" | \"Hubungan\" | \"Pribadi\" | \"Keluarga\" | \"Lainnya\"] (pilih minimal 1, maksimal 3 kategori catatan harian yang relevan),\n"
                . "  \"analysis\": \"1-2 kalimat analisis psikologis/kondisi wellbeing pengguna secara empati dan bersahabat\",\n"
                . "  \"appreciation\": \"1 kalimat apresiasi hangat dan dorongan semangat atas apa yang dicapai/dilalui pengguna hari ini\"\n"
                . "}\n\n"
                . "Kamu HARUS merespon HANYA dengan dokumen JSON mentah tanpa format markdown (jangan pakai ```json atau blok kode lainnya).";

            $aiResponse = $aiService->chat([
                'system' => $systemPrompt,
                'message' => $request->message,
                'temperature' => 0.2,
            ]);

            // Decode response
            $cleanContent = trim($aiResponse->content);
            
            // Remove markdown code blocks if any
            if (str_starts_with($cleanContent, '```')) {
                $cleanContent = preg_replace('/^```(?:json)?|```$/m', '', $cleanContent);
                $cleanContent = trim($cleanContent);
            }

            $data = json_decode($cleanContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Gagal melakukan parsing JSON dari respon AI: " . $cleanContent);
            }

            $userId = Auth::id();
            $currentWeekStr = now()->format('Y-\WW');

            // 1. Simpan/Update Daily Check-in
            $checkinData = $data['checkin'] ?? [];
            if (!empty($checkinData)) {
                WellbeingCheckin::updateOrCreate(
                    ['user_id' => $userId, 'checkin_date' => now()->toDateString()],
                    array_intersect_key($checkinData, array_flip(['mood', 'energy', 'mental_load', 'rest_condition', 'thoughts']))
                );
            }

            // 2. Simpan Jurnal Refleksi Baru
            $journalData = $data['journal'] ?? [];
            if (!empty($journalData) && (!empty($journalData['feeling']) || !empty($journalData['today_event']))) {
                WellbeingJournal::create([
                    'user_id' => $userId,
                    'raw_content' => $request->message,
                    'feeling' => $journalData['feeling'] ?? null,
                    'today_event' => $journalData['today_event'] ?? null,
                    'gratitude' => $journalData['gratitude'] ?? null,
                    'let_go' => $journalData['let_go'] ?? null,
                    'improvement' => $journalData['improvement'] ?? null,
                    'analysis' => $data['analysis'] ?? null,
                    'appreciation' => $data['appreciation'] ?? null,
                    'categories' => $data['categories'] ?? ['Pribadi'],
                ]);
            }

            // 3. Simpan Target Hidup Baru (Goals)
            $goalsData = $data['goals'] ?? [];
            $savedGoalsList = [];
            foreach ($goalsData as $goal) {
                if (!empty($goal['title']) && !empty($goal['category'])) {
                    WellbeingGoal::create([
                        'user_id' => $userId,
                        'title' => $goal['title'],
                        'category' => $goal['category'],
                        'is_completed' => false,
                    ]);
                    $savedGoalsList[] = "- [{$goal['category']}] {$goal['title']}";
                }
            }

            // 4. Simpan/Update Refleksi Mingguan
            $reflectionData = $data['reflection'] ?? [];
            if (!empty($reflectionData) && (!empty($reflectionData['what_went_well']) || !empty($reflectionData['what_proud_of']))) {
                $summary = "Minggu ini, hal yang berjalan baik adalah: " . ($reflectionData['what_went_well'] ?? '-') 
                         . ". Dan hal yang cukup melelahkan adalah: " . ($reflectionData['what_was_exhausting'] ?? '-') 
                         . ". Namun, hal yang dibanggakan: " . ($reflectionData['what_proud_of'] ?? '-') . ".";

                WellbeingReflection::updateOrCreate(
                    ['user_id' => $userId, 'week_number' => $currentWeekStr],
                    array_merge([
                        'summary' => $summary,
                    ], array_intersect_key($reflectionData, array_flip(['what_went_well', 'what_was_exhausting', 'what_to_change', 'what_proud_of'])))
                );
            }

            return response()->json([
                'success' => true,
                'checkin' => "Mood: " . ($checkinData['mood'] ?? 'Stabil') . ", Energi: " . ($checkinData['energy'] ?? '3') . "/5, Istirahat: " . ($checkinData['rest_condition'] ?? '3') . "/5",
                'checkin_data' => $checkinData,
                'journal' => "Jurnal harian tersimpan aman.",
                'goals' => !empty($savedGoalsList) ? $savedGoalsList : ["Tidak ada target baru yang terdeteksi"],
                'reflection' => "Refleksi Mingguan berhasil diupdate.",
                'new_entry' => [
                    'date' => now()->isoFormat('dddd, D MMMM Y - HH:mm'),
                    'raw_content' => $request->message,
                    'analysis' => $data['analysis'] ?? null,
                    'appreciation' => $data['appreciation'] ?? null,
                    'categories' => $data['categories'] ?? ['Pribadi'],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses Auto-Journaler: ' . $e->getMessage(),
            ], 500);
        }
    }
}