<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\WellbeingCheckin;
use App\Models\WellbeingGoal;
use App\Models\WellbeingJournal;
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    public function index(): View
    {
        // Hanya tampilkan perusahaan yang ditugaskan ke user management yang login
        $companies = CompanyProfile::where('manager_id', Auth::id())->latest()->paginate(10);

        return view('management.company.index', compact('companies'));
    }

    public function show(CompanyProfile $company): View
    {
        $this->authorizeAccess($company);

        return view('management.company.show', compact('company'));
    }

    public function edit(CompanyProfile $company): View
    {
        $this->authorizeAccess($company);

        return view('management.company.edit', compact('company'));
    }

    public function update(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeAccess($company);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:company_profiles,slug,'.$company->id],
            'is_published' => ['nullable', 'boolean'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'about' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'social_media' => ['nullable', 'array'],
            'dynamic_sections' => ['nullable', 'array'],
            'dynamic_files.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $validated['is_published'] = $request->has('is_published');
        $validated['slug'] = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->company_name);

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($company->banner && Storage::disk('public')->exists($company->banner)) {
                Storage::disk('public')->delete($company->banner);
            }
            $validated['banner'] = $request->file('banner')->store('company', 'public');
        }

        $dynamicSections = $request->input('dynamic_sections', []);
        if ($request->hasFile('dynamic_files')) {
            foreach ($request->file('dynamic_files') as $index => $file) {
                if (isset($dynamicSections[$index])) {
                    $dynamicSections[$index]['file_url'] = $file->store('company/dynamic', 'public');
                }
            }
        }

        if ($company->dynamic_sections) {
            foreach ($dynamicSections as $index => $section) {
                if (empty($section['file_url']) && isset($company->dynamic_sections[$index]['file_url'])) {
                    $dynamicSections[$index]['file_url'] = $company->dynamic_sections[$index]['file_url'];
                }
            }
        }

        $validated['dynamic_sections'] = array_values($dynamicSections);
        $validated['social_media'] = array_values($request->input('social_media', []));

        $company->update($validated);

        return redirect()->route('management.company.show', $company->id)
            ->with('success', 'Portofolio publik dan data profil berhasil diperbarui.');
    }

    // Proteksi keamanan: pastikan perusahaan memang milik manager tersebut
    private function authorizeAccess(CompanyProfile $company): void
    {
        if ($company->manager_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda bukan pengelola yang ditugaskan untuk perusahaan ini.');
        }
    }

    public function workspace(CompanyProfile $company): View
    {
        $this->authorizeAccess($company);

        // Ambil semua proyek yang terkait dengan company ini
        $projects = Project::where('company_profile_id', $company->id)->latest()->get();

        // Ambil list semua member tim internal yang ter-embed di company ini
        $teamMembers = User::where('company_profile_id', $company->id)->get();

        // Ambil ID semua anggota tim yang berpartisipasi dalam proyek company ini
        $employeeIds = [];
        foreach ($projects as $pj) {
            $matrix = $pj->team_matrix ?? [];
            foreach ($matrix as $row) {
                if (isset($row['user_id'])) {
                    $employeeIds[] = (int) $row['user_id'];
                }
            }
        }
        $employeeIds = array_unique($employeeIds);

        // Ambil data checkin wellbeing 7 hari terakhir dari para karyawan tersebut
        $wellbeingCheckins = WellbeingCheckin::whereIn('user_id', $employeeIds)
            ->whereDate('checkin_date', '>=', now()->subDays(7)->toDateString())
            ->get();

        $avgEnergy = $wellbeingCheckins->avg('energy') ?? 3.5;
        $avgMental = $wellbeingCheckins->avg('mental_load') ?? 2.8;
        $avgRest = $wellbeingCheckins->avg('rest_condition') ?? 3.2;

        $moodCounts = [];
        $totalCheckins = $wellbeingCheckins->count();
        if ($totalCheckins > 0) {
            foreach ($wellbeingCheckins as $chk) {
                if ($chk->mood) {
                    $moodCounts[$chk->mood] = ($moodCounts[$chk->mood] ?? 0) + 1;
                }
            }
        }

        $moodPercentages = [];
        foreach ($moodCounts as $mood => $count) {
            $moodPercentages[$mood] = round(($count / $totalCheckins) * 100);
        }

        // Hitung resiko Burnout berdasarkan beban kerja dan beban mental
        $companyTasks = ProjectTask::whereIn('project_id', $projects->pluck('id'))->get();
        $overdueTasks = $companyTasks->where('status', '!=', 'Completed')
            ->whereNotNull('due_date')
            ->filter(fn ($t) => $t->due_date->isPast())
            ->count();

        $burnoutRisk = 'Rendah';
        if ($avgMental > 3.5 || $overdueTasks > 3) {
            $burnoutRisk = 'Tinggi';
        } elseif ($avgMental > 3.0 || $overdueTasks > 0) {
            $burnoutRisk = 'Sedang';
        }

        return view('management.company.workspace', compact(
            'company',
            'projects',
            'teamMembers',
            'avgEnergy',
            'avgMental',
            'avgRest',
            'moodPercentages',
            'burnoutRisk',
            'overdueTasks'
        ));
    }

    public function wellbeingReport(CompanyProfile $company): View
    {
        $this->authorizeAccess($company);

        // Ambil semua proyek yang terkait dengan company ini
        $projects = Project::where('company_profile_id', $company->id)->latest()->get();

        // Ambil ID semua anggota tim yang berpartisipasi dalam proyek company ini
        $employeeIds = [];
        foreach ($projects as $pj) {
            $matrix = $pj->team_matrix ?? [];
            foreach ($matrix as $row) {
                if (isset($row['user_id'])) {
                    $employeeIds[] = (int) $row['user_id'];
                }
            }
        }
        $employeeIds = array_unique($employeeIds);

        // Ambil data checkin wellbeing 30 hari terakhir dari para karyawan tersebut
        $wellbeingCheckins = WellbeingCheckin::whereIn('user_id', $employeeIds)
            ->whereDate('checkin_date', '>=', now()->subDays(30)->toDateString())
            ->get();

        $avgEnergy = $wellbeingCheckins->avg('energy') ?? 3.5;
        $avgMental = $wellbeingCheckins->avg('mental_load') ?? 2.8;
        $avgRest = $wellbeingCheckins->avg('rest_condition') ?? 3.2;

        $moodCounts = [];
        $totalCheckins = $wellbeingCheckins->count();
        if ($totalCheckins > 0) {
            foreach ($wellbeingCheckins as $chk) {
                if ($chk->mood) {
                    $moodCounts[$chk->mood] = ($moodCounts[$chk->mood] ?? 0) + 1;
                }
            }
        }

        $moodPercentages = [];
        foreach ($moodCounts as $mood => $count) {
            $moodPercentages[$mood] = round(($count / $totalCheckins) * 100);
        }

        // Hitung resiko Burnout berdasarkan beban kerja dan beban mental
        $companyTasks = ProjectTask::whereIn('project_id', $projects->pluck('id'))->get();
        $overdueTasks = $companyTasks->where('status', '!=', 'Completed')
            ->whereNotNull('due_date')
            ->filter(fn ($t) => $t->due_date->isPast())
            ->count();

        $burnoutRisk = 'Rendah';
        if ($avgMental > 3.5 || $overdueTasks > 3) {
            $burnoutRisk = 'Tinggi';
        } elseif ($avgMental > 3.0 || $overdueTasks > 0) {
            $burnoutRisk = 'Sedang';
        }

        // Hitung aktivitas tools (anonymized)
        $journalsCount = WellbeingJournal::whereIn('user_id', $employeeIds)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->count();

        $goalsCount = WellbeingGoal::whereIn('user_id', $employeeIds)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->count();

        // Rekomendasi Manajerial AI
        $recommendations = [];
        $aiUsed = false;

        try {
            $aiService = app(AIService::class);

            $moodText = '';
            foreach ($moodPercentages as $mood => $pct) {
                $moodText .= "- Mood {$mood}: {$pct}%\n";
            }

            $prompt = "Berikut adalah data kesehatan agregat tim selama 30 hari terakhir:\n"
                ."- Rata-rata Energi Tim: {$avgEnergy} / 5.0\n"
                ."- Rata-rata Beban Mental Tim: {$avgMental} / 5.0\n"
                ."- Rata-rata Kondisi Istirahat: {$avgRest} / 5.0\n"
                ."- Risiko Burnout Tim: {$burnoutRisk}\n"
                ."- Jumlah Tugas Terlambat: {$overdueTasks}\n"
                ."- Aktivitas Menulis Jurnal Mandiri: {$journalsCount} log\n"
                ."- Jumlah Target Keseimbangan Hidup Terdaftar: {$goalsCount}\n"
                ."Sebaran Emosi:\n"
                .$moodText."\n"
                .'Berikan 3 sampai 4 poin rekomendasi manajerial yang spesifik, solutif, suportif, dan taktis dalam bahasa Indonesia untuk meningkatkan kesejahteraan tim. Jawab langsung dalam format daftar poin teks polos dipisah tanda baris baru (newline) tanpa menggunakan bullet points (seperti - atau * atau angka) dan tanpa format markdown atau backticks.';

            $aiResponse = $aiService->chat([
                'system' => 'Kamu adalah Konsultan Kesehatan Organisasi & AI Manajer Wellbeing yang empati dan ahli.',
                'message' => $prompt,
                'temperature' => 0.6,
            ]);

            $lines = explode("\n", $aiResponse->content);
            foreach ($lines as $line) {
                $line = trim(preg_replace('/^\s*[-*•\d\.]+\s+/', '', $line));
                if (! empty($line)) {
                    $recommendations[] = $line;
                }
            }

            if (! empty($recommendations)) {
                $aiUsed = true;
            }

        } catch (\Exception $e) {
            Log::warning('AI Wellbeing Report generator fallback triggered: '.$e->getMessage());
        }

        // Fallback jika AI gagal atau belum dikonfigurasi
        if (empty($recommendations)) {
            if ($avgMental > 3.2) {
                $recommendations[] = 'Beban mental tim terpantau meningkat. Direkomendasikan untuk meninjau pembagian tugas proyek dan mengurangi rapat koordinasi yang kurang penting.';
            }
            if ($avgEnergy < 3.2) {
                $recommendations[] = 'Tingkat energi tim menurun di bawah rata-rata. Pertimbangkan memberikan waktu istirahat (work-free days) atau membatasi lembur di akhir pekan.';
            }
            if ($overdueTasks > 2) {
                $recommendations[] = 'Terdapat beberapa tugas penting yang melewati batas tenggat. Silakan diskusikan kendala teknis dengan PIC proyek sebelum menumpuk tugas baru.';
            }
            if (empty($recommendations)) {
                $recommendations[] = 'Keseimbangan ritme kerja dan kesehatan mental tim Anda dalam kondisi prima. Jaga ritme kerja kolaboratif ini agar tetap berkelanjutan.';
            }
        }

        return view('management.company.wellbeing', compact(
            'company',
            'projects',
            'avgEnergy',
            'avgMental',
            'avgRest',
            'moodPercentages',
            'burnoutRisk',
            'overdueTasks',
            'journalsCount',
            'goalsCount',
            'recommendations',
            'totalCheckins',
            'aiUsed'
        ));
    }

    public function generateCompanyAi(Request $request, CompanyProfile $company)
    {
        $userId = Auth::id();
        if ($company->manager_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to company profile',
            ], 403);
        }

        $promptText = $request->input('prompt');

        try {
            $aiService = app(AIService::class);

            $systemInstruction = "Kamu adalah LUNOU, asisten AI profil perusahaan Yoimo. Tugasmu adalah merancang profil perusahaan yang profesional dan menarik (Tagline, About, Vision, Mission, Social Media Platforms, dan Dynamic Sections).\n\n"
                ."Response HARUS berupa objek JSON dengan key berikut:\n"
                ."- tagline: Slogan / Tagline singkat perusahaan.\n"
                ."- about: Narasi tentang perusahaan (1 paragraf ringkas).\n"
                ."- vision: Visi perusahaan.\n"
                ."- mission: Misi perusahaan (tiap misi pisahkan dengan baris baru atau nomor).\n"
                ."- social_media: array berisi platform sosmed bawaan (contoh: [{'platform': 'LinkedIn', 'url': 'https://linkedin.com/company/nama'}, {'platform': 'Instagram', 'url': 'https://instagram.com/nama'}]).\n"
                ."- dynamic_sections: array berisi 1-2 blok modul promosi kustom (contoh: [{'title': 'Keunggulan Kami', 'type': 'text', 'content': 'Kami mengutamakan kualitas kode dan kepuasan klien dalam setiap delivery.'}]).\n\n"
                .'Pastikan format response Anda hanya berupa raw JSON valid tanpa markdown formatting atau pembungkus kode.';

            $companyContext = "Informasi Perusahaan:\n"
                ."- Nama Perusahaan: {$company->company_name}\n"
                ."- Tagline Saat Ini: {$company->tagline}\n"
                ."- Tentang: {$company->about}\n"
                ."- Instruksi Tambahan User: {$promptText}";

            $res = $aiService->chat([
                'system' => $systemInstruction,
                'message' => $companyContext,
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
                'message' => 'Gagal merancang profil perusahaan: '.$e->getMessage(),
            ], 500);
        }
    }
}
