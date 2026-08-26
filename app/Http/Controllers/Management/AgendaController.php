<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Mail\AgendaNotificationMail;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectAgenda;
use App\Models\ProjectRoadmap;
use App\Models\User;
use App\Notifications\AgendaNotification;
use App\Services\AIService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $project->load(['agendas.creator', 'company']);

        $selectedCategory = $request->query('category');
        $agendasQuery = $project->agendas();

        if ($selectedCategory) {
            $agendasQuery->where('category', $selectedCategory);
        }

        $agendas = $agendasQuery->latest()->get();

        $assignedUserIds = collect($project->team_matrix ?? [])->pluck('user_id')->unique();
        $teamMembers = User::whereIn('id', $assignedUserIds)->get();
        if ($teamMembers->isEmpty()) {
            $teamMembers = User::whereIn('role', ['user', 'finance', 'management'])->get();
        }

        return view('management.projects.agendas.index', compact('project', 'agendas', 'teamMembers', 'selectedCategory'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'recurrence' => ['required', 'in:once,daily,weekly,monthly'],
            'recurrence_days' => ['nullable', 'string'],
            'location_type' => ['required', 'in:online,offline'],
            'meeting_url' => ['nullable', 'url', 'max:255'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'attendee_ids' => ['nullable', 'array'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'Scheduled';
        $validated['attendee_ids'] = $request->input('attendee_ids', []);

        $agenda = ProjectAgenda::create($validated);
        $agenda->load('project');

        $attendeeIds = $validated['attendee_ids'] ?? [];
        if (! empty($attendeeIds)) {
            $users = User::whereIn('id', $attendeeIds)->get();
            foreach ($users as $u) {
                $u->notify(new AgendaNotification($agenda, 'scheduled'));

                if ($u->email) {
                    try {
                        Mail::to($u->email)->send(new AgendaNotificationMail($agenda, 'scheduled'));
                    } catch (\Exception $e) {
                        Log::error("Failed to send Agenda mail to {$u->email}: ".$e->getMessage());
                    }
                }

                // WhatsApp Notification
                $formattedDate = date('d M Y', strtotime($agenda->start_date));
                $formattedTime = $agenda->start_time ? date('H:i', strtotime($agenda->start_time)) : 'Belum Ditentukan';
                $location = $agenda->location_type === 'online'
                    ? "Virtual Online (Link: {$agenda->meeting_url})"
                    : ($agenda->location_address ?: 'Offline');

                $msg = "📅 *AGENDA KEGIATAN BARU*\n\n"
                    .'Halo, Anda diundang ke agenda berikut pada proyek *'.($agenda->project->name ?? 'Project')."*:\n"
                    ."• *Agenda*: {$agenda->title}\n"
                    ."• *Kategori*: {$agenda->category}\n"
                    ."• *Waktu*: {$formattedDate} jam {$formattedTime} WIB\n"
                    ."• *Lokasi*: {$location}\n\n"
                    .'Harap hadir tepat waktu ya!';
                WhatsAppService::send($u->phone, $msg);
            }
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Agenda', 'CREATE', "Menjadwalkan agenda baru: '{$validated['title']}' ({$validated['category']})");

        return redirect()->route('management.projects.agendas.index', $project->id)
            ->with('success', 'Agenda kegiatan baru berhasil dijadwalkan.');
    }

    public function updateStatus(Request $request, Project $project, ProjectAgenda $agenda): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:Scheduled,Ongoing,Completed,Cancelled'],
            'agenda_notes' => ['nullable', 'string'],
        ]);

        $agenda->update([
            'status' => $request->status,
            'agenda_notes' => $request->agenda_notes ?? $agenda->agenda_notes,
        ]);

        $agenda->load('project');
        $attendeeIds = $agenda->attendee_ids ?? [];
        if (! empty($attendeeIds)) {
            $users = User::whereIn('id', $attendeeIds)->get();
            foreach ($users as $u) {
                $u->notify(new AgendaNotification($agenda, 'updated'));

                if ($u->email) {
                    try {
                        Mail::to($u->email)->send(new AgendaNotificationMail($agenda, 'updated'));
                    } catch (\Exception $e) {
                        Log::error("Failed to send Agenda update mail to {$u->email}: ".$e->getMessage());
                    }
                }

                // WhatsApp Notification
                $formattedDate = date('d M Y', strtotime($agenda->start_date));
                $formattedTime = $agenda->start_time ? date('H:i', strtotime($agenda->start_time)) : 'Belum Ditentukan';
                $msg = "🔔 *STATUS AGENDA DIPERBARUI*\n\n"
                    ."Halo, informasi agenda Anda telah diperbarui:\n"
                    ."• *Agenda*: {$agenda->title}\n"
                    ."• *Status Baru*: {$agenda->status}\n"
                    ."• *Waktu*: {$formattedDate} jam {$formattedTime} WIB\n"
                    .'• *Notulensi/Catatan*: '.($agenda->agenda_notes ?: '-')."\n\n"
                    .'Silakan cek di dashboard Yoimo.';
                WhatsAppService::send($u->phone, $msg);
            }
        }

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Agenda', 'UPDATE', "Memperbarui status/notulensi agenda: '{$agenda->title}' menjadi {$request->status}");

        return back()->with('success', 'Status dan catatan agenda berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectAgenda $agenda): RedirectResponse
    {
        $title = $agenda->title;
        $agenda->delete();

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Agenda', 'DELETE', "Menghapus agenda dari jadwal: '{$title}'");

        return redirect()->route('management.projects.agendas.index', $project->id)
            ->with('success', 'Agenda berhasil dihapus dari jadwal.');
    }

    public function generateAgendaAi(Request $request, Project $project)
    {
        $userId = Auth::id();
        if ($project->created_by !== $userId && ($project->company && $project->company->manager_id !== $userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to project',
            ], 403);
        }

        $promptText = $request->input('prompt');

        // Fetch team members list
        $assignedUserIds = collect($project->team_matrix ?? [])->pluck('user_id')->unique();
        $teamMembers = User::whereIn('id', $assignedUserIds)->get();
        if ($teamMembers->isEmpty()) {
            $teamMembers = User::whereIn('role', ['user', 'finance', 'management'])->get();
        }

        $membersList = '';
        foreach ($teamMembers as $u) {
            $membersList .= "- ID: {$u->id}, Nama: {$u->name}, Email: {$u->email}, Role: {$u->role}\n";
        }

        try {
            $aiService = app(AIService::class);

            $systemInstruction = "Kamu adalah LUNOU, asisten AI jadwal Yoimo. Tugasmu adalah menganalisis instruksi user dan menyusun parameter agenda/acara yang relevan.\n\n"
                ."Response HARUS berupa objek JSON dengan key berikut:\n"
                ."- title: Judul agenda yang ringkas dan profesional.\n"
                ."- category: Salah satu dari kategori berikut secara persis:\n"
                ."  'Meeting Online (Google Meet / Zoom)'\n"
                ."  'Meeting Offline (Tatap Muka)'\n"
                ."  'Olahraga & Kesehatan'\n"
                ."  'Liburan & Outing'\n"
                ."  'Nonton & Hiburan'\n"
                ."  'Roadshow & Kunjungan'\n"
                ."  'Workshop & Pelatihan'\n"
                ."- description: Deskripsi/Rundown/Susunan Acara singkat.\n"
                ."- recurrence: Salah satu dari: 'once', 'daily', 'weekly', 'monthly'.\n"
                .'- start_date: Tanggal mulai format YYYY-MM-DD. Gunakan tanggal hari ini ('.now()->toDateString().") sebagai acuan jika disebut 'besok' (hitung +1 hari) atau hari tertentu.\n"
                ."- start_time: Jam mulai format HH:MM.\n"
                ."- location_type: Salah satu dari: 'online', 'offline'.\n"
                ."- meeting_url: URL rapat jika online (contoh: https://meet.google.com/abc-defg-hij) atau kosong.\n"
                ."- location_address: Alamat lokasi jika offline atau kosong.\n"
                ."- attendee_ids: Array berisi ID numerik anggota tim yang paling relevan untuk diundang berdasarkan instruksi user.\n\n"
                ."Daftar Tim Anggota yang tersedia:\n{$membersList}\n\n"
                .'Pastikan format response Anda hanya berupa raw JSON valid tanpa markdown formatting atau pembungkus kode.';

            $projectContext = "Informasi Proyek:\n"
                ."- Nama Proyek: {$project->name}\n"
                ."- Kategori Proyek: {$project->category}\n"
                ."- Tahap Proyek: {$project->current_stage}\n"
                ."- Instruksi User: {$promptText}";

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
                'message' => 'Gagal merancang agenda: '.$e->getMessage(),
            ], 500);
        }
    }

    public function generateBulkAgendasAi(Request $request, Project $project)
    {
        $userId = Auth::id();
        $roadmaps = ProjectRoadmap::where('project_id', $project->id)->orderBy('start_date')->get();

        if ($roadmaps->isEmpty()) {
            return back()->with('error', 'Silakan buat alur linimasa/roadmap proyek terlebih dahulu sebelum men-generate agenda otomatis.');
        }

        $projectName = $project->name;
        $projectBrief = $project->description ?: 'Tidak ada deskripsi detail.';
        $customInstruction = $request->input('instruction') ?: 'Tidak ada instruksi khusus.';

        // Fetch team members list
        $assignedUserIds = collect($project->team_matrix ?? [])->pluck('user_id')->unique();
        $teamMembers = User::whereIn('id', $assignedUserIds)->get();
        if ($teamMembers->isEmpty()) {
            $teamMembers = User::whereIn('role', ['user', 'finance', 'management'])->get();
        }

        $membersList = '';
        foreach ($teamMembers as $u) {
            $membersList .= "- ID: {$u->id}, Nama: {$u->name}, Email: {$u->email}, Role: {$u->role}\n";
        }

        // Build Roadmap details for prompt context
        $roadmapContext = '';
        foreach ($roadmaps as $idx => $rm) {
            $roadmapContext .= "FASE INDEX {$idx}:\n"
                ."- Judul: {$rm->title}\n"
                ."- Deskripsi: {$rm->description}\n"
                .'- Tanggal Mulai: '.$rm->start_date->format('Y-m-d')."\n"
                .'- Tanggal Selesai: '.$rm->end_date->format('Y-m-d')."\n\n";
        }

        $systemPrompt = "Anda adalah Asisten Proyek AI bernama LUNOU yang bertindak sebagai Senior IT Project Manager dan Scrum Master profesional. Anda menguasai Systems Thinking, Design Thinking, dan Human-First.\n\n"
            ."Tugas Anda adalah menyusun kalender agenda pertemuan rutin (seperti Daily Standup, Weekly Progress Review, Sprint Planning, Sprint Retrospective) secara otomatis berbasis rentang waktu proyek dan alur linimasa (roadmap) berikut.\n\n"
            ."DETAIL PROYEK:\n"
            ."- Nama Proyek: {$projectName}\n"
            ."- Deskripsi/Brief Proyek: {$projectBrief}\n\n"
            ."ALUR LINIMASA (ROADMAP) PROYEK:\n"
            ."{$roadmapContext}"
            ."INSTRUKSI KHUSUS DARI USER:\n"
            ."{$customInstruction}\n\n"
            ."ATURAN MERANCANG AGENDA/MEETING:\n"
            ."1. Rancanglah pertemuan rutin mingguan (Weekly Progress Review) di setiap akhir minggu selama masa proyek, dan pertemuan koordinasi penting di awal dan akhir setiap fase linimasa.\n"
            ."2. Setiap agenda harus memiliki detail judul yang profesional, rundown/agenda singkat yang informatif.\n"
            ."3. Tentukan kategori kegiatan secara persis. Pilihan kategori: 'Meeting Online', 'Meeting Offline', 'Workshop & Pelatihan', 'Lainnya'.\n"
            ."4. Tanggal mulai (`start_date`) wajib berada di dalam rentang tanggal proyek/fase.\n"
            ."5. Tentukan jam mulai (`start_time`) dan jam selesai (`end_time`) dalam format HH:MM secara masuk akal (misal 09:00 hingga 10:00).\n"
            ."6. Jika kategori 'Meeting Online', set `location_type` menjadi 'online' dan sediakan `meeting_url` berupa link mock (misal: https://meet.google.com/abc-defg-hij).\n"
            ."7. Pilih anggota tim yang paling relevan untuk diundang (`attendee_ids`) berupa array ID user dari daftar di bawah.\n"
            ."8. Kembalikan hasilnya dalam format JSON murni berupa array objek dengan struktur:\n"
            ."[\n"
            ."  {\n"
            ."    \"title\": \"Judul Agenda Rapat\",\n"
            ."    \"description\": \"Deskripsi atau agenda bahasan...\",\n"
            ."    \"category\": \"Meeting Online\",\n"
            ."    \"start_date\": \"YYYY-MM-DD\",\n"
            ."    \"end_date\": \"YYYY-MM-DD\",\n"
            ."    \"start_time\": \"09:00\",\n"
            ."    \"end_time\": \"10:00\",\n"
            ."    \"location_type\": \"online\",\n"
            ."    \"meeting_url\": \"https://meet.google.com/abc-defg-hij\",\n"
            ."    \"location_address\": null,\n"
            ."    \"attendee_ids\": [1, 2]\n"
            ."  },\n"
            ."  ...\n"
            ."]\n"
            ."Daftar Tim Anggota yang tersedia:\n{$membersList}\n"
            .'Jangan sertakan markdown, pembungkus ```json, atau penjelasan lainnya.';

        try {
            $aiService = app(AIService::class);
            $response = $aiService->chat([
                'system' => 'Anda adalah Asisten Proyek AI bernama LUNOU. Tugas Anda adalah menyusun kalender pertemuan rutin proyek IT berformat JSON murni.',
                'message' => $systemPrompt,
                'temperature' => 0.2,
            ]);

            $content = trim($response->content);
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
            }
            $content = trim($content);

            $agendas = json_decode($content, true);

            if (! is_array($agendas)) {
                throw new \Exception('Format respon AI tidak valid: '.$content);
            }

            $createdCount = 0;
            foreach ($agendas as $agendaData) {
                ProjectAgenda::create([
                    'project_id' => $project->id,
                    'created_by' => $userId,
                    'title' => $agendaData['title'],
                    'description' => $agendaData['description'] ?? null,
                    'category' => $agendaData['category'] ?? 'Meeting Online',
                    'start_date' => $agendaData['start_date'],
                    'end_date' => $agendaData['end_date'] ?? $agendaData['start_date'],
                    'start_time' => $agendaData['start_time'] ?? null,
                    'end_time' => $agendaData['end_time'] ?? null,
                    'recurrence' => 'once',
                    'location_type' => $agendaData['location_type'] ?? 'online',
                    'meeting_url' => $agendaData['meeting_url'] ?? null,
                    'location_address' => $agendaData['location_address'] ?? null,
                    'attendee_ids' => $agendaData['attendee_ids'] ?? [],
                    'status' => 'Scheduled',
                ]);
                $createdCount++;
            }

            // Catat Audit Log
            ProjectActivityLog::record($project->id, 'Agenda', 'CREATE', "Men-generate secara otomatis {$createdCount} agenda pertemuan rutin proyek menggunakan LUNOU AI.");

            return redirect()->route('management.projects.agendas.index', $project->id)
                ->with('success', "Berhasil men-generate {$createdCount} agenda baru secara otomatis menggunakan LUNOU AI.");

        } catch (\Exception $e) {
            Log::error('Failed to generate Agendas via AI: '.$e->getMessage());

            return back()->with('error', 'Gagal memproses pembuatan agenda otomatis: '.$e->getMessage());
        }
    }
}
