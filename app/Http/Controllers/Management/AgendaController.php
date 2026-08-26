<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectAgenda;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'category'         => ['required', 'string'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time'       => ['nullable'],
            'end_time'         => ['nullable'],
            'recurrence'       => ['required', 'in:once,daily,weekly,monthly'],
            'recurrence_days'  => ['nullable', 'string'],
            'location_type'    => ['required', 'in:online,offline'],
            'meeting_url'      => ['nullable', 'url', 'max:255'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'attendee_ids'     => ['nullable', 'array'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'Scheduled';
        $validated['attendee_ids'] = $request->input('attendee_ids', []);

        $agenda = ProjectAgenda::create($validated);
        $agenda->load('project');

        $attendeeIds = $validated['attendee_ids'] ?? [];
        if (!empty($attendeeIds)) {
            $users = User::whereIn('id', $attendeeIds)->get();
            foreach ($users as $u) {
                if ($u->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($u->email)->send(new \App\Mail\AgendaNotificationMail($agenda, 'scheduled'));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send Agenda mail to {$u->email}: " . $e->getMessage());
                    }
                }

                // WhatsApp Notification
                $formattedDate = date('d M Y', strtotime($agenda->start_date));
                $formattedTime = $agenda->start_time ? date('H:i', strtotime($agenda->start_time)) : 'Belum Ditentukan';
                $location = $agenda->location_type === 'online' 
                    ? "Virtual Online (Link: {$agenda->meeting_url})" 
                    : ($agenda->location_address ?: 'Offline');

                $msg = "📅 *AGENDA KEGIATAN BARU*\n\n"
                    . "Halo, Anda diundang ke agenda berikut pada proyek *" . ($agenda->project->name ?? 'Project') . "*:\n"
                    . "• *Agenda*: {$agenda->title}\n"
                    . "• *Kategori*: {$agenda->category}\n"
                    . "• *Waktu*: {$formattedDate} jam {$formattedTime} WIB\n"
                    . "• *Lokasi*: {$location}\n\n"
                    . "Harap hadir tepat waktu ya!";
                \App\Services\WhatsAppService::send($u->phone, $msg);
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
            'status'       => ['required', 'in:Scheduled,Ongoing,Completed,Cancelled'],
            'agenda_notes' => ['nullable', 'string']
        ]);

        $agenda->update([
            'status'       => $request->status,
            'agenda_notes' => $request->agenda_notes ?? $agenda->agenda_notes
        ]);

        $agenda->load('project');
        $attendeeIds = $agenda->attendee_ids ?? [];
        if (!empty($attendeeIds)) {
            $users = User::whereIn('id', $attendeeIds)->get();
            foreach ($users as $u) {
                if ($u->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($u->email)->send(new \App\Mail\AgendaNotificationMail($agenda, 'updated'));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send Agenda update mail to {$u->email}: " . $e->getMessage());
                    }
                }

                // WhatsApp Notification
                $formattedDate = date('d M Y', strtotime($agenda->start_date));
                $formattedTime = $agenda->start_time ? date('H:i', strtotime($agenda->start_time)) : 'Belum Ditentukan';
                $msg = "🔔 *STATUS AGENDA DIPERBARUI*\n\n"
                    . "Halo, informasi agenda Anda telah diperbarui:\n"
                    . "• *Agenda*: {$agenda->title}\n"
                    . "• *Status Baru*: {$agenda->status}\n"
                    . "• *Waktu*: {$formattedDate} jam {$formattedTime} WIB\n"
                    . "• *Notulensi/Catatan*: " . ($agenda->agenda_notes ?: '-') . "\n\n"
                    . "Silakan cek di dashboard Yoimo.";
                \App\Services\WhatsAppService::send($u->phone, $msg);
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
                'message' => 'Unauthorized access to project'
            ], 403);
        }

        $promptText = $request->input('prompt');

        // Fetch team members list
        $assignedUserIds = collect($project->team_matrix ?? [])->pluck('user_id')->unique();
        $teamMembers = User::whereIn('id', $assignedUserIds)->get();
        if ($teamMembers->isEmpty()) {
            $teamMembers = User::whereIn('role', ['user', 'finance', 'management'])->get();
        }

        $membersList = "";
        foreach ($teamMembers as $u) {
            $membersList .= "- ID: {$u->id}, Nama: {$u->name}, Email: {$u->email}, Role: {$u->role}\n";
        }

        try {
            $aiService = app(\App\Services\AIService::class);
            
            $systemInstruction = "Kamu adalah LUNOU, asisten AI jadwal Yoimo. Tugasmu adalah menganalisis instruksi user dan menyusun parameter agenda/acara yang relevan.\n\n"
                . "Response HARUS berupa objek JSON dengan key berikut:\n"
                . "- title: Judul agenda yang ringkas dan profesional.\n"
                . "- category: Salah satu dari kategori berikut secara persis:\n"
                . "  'Meeting Online (Google Meet / Zoom)'\n"
                . "  'Meeting Offline (Tatap Muka)'\n"
                . "  'Olahraga & Kesehatan'\n"
                . "  'Liburan & Outing'\n"
                . "  'Nonton & Hiburan'\n"
                . "  'Roadshow & Kunjungan'\n"
                . "  'Workshop & Pelatihan'\n"
                . "- description: Deskripsi/Rundown/Susunan Acara singkat.\n"
                . "- recurrence: Salah satu dari: 'once', 'daily', 'weekly', 'monthly'.\n"
                . "- start_date: Tanggal mulai format YYYY-MM-DD. Gunakan tanggal hari ini (" . now()->toDateString() . ") sebagai acuan jika disebut 'besok' (hitung +1 hari) atau hari tertentu.\n"
                . "- start_time: Jam mulai format HH:MM.\n"
                . "- location_type: Salah satu dari: 'online', 'offline'.\n"
                . "- meeting_url: URL rapat jika online (contoh: https://meet.google.com/abc-defg-hij) atau kosong.\n"
                . "- location_address: Alamat lokasi jika offline atau kosong.\n"
                . "- attendee_ids: Array berisi ID numerik anggota tim yang paling relevan untuk diundang berdasarkan instruksi user.\n\n"
                . "Daftar Tim Anggota yang tersedia:\n{$membersList}\n\n"
                . "Pastikan format response Anda hanya berupa raw JSON valid tanpa markdown formatting atau pembungkus kode.";

            $projectContext = "Informasi Proyek:\n"
                . "- Nama Proyek: {$project->name}\n"
                . "- Kategori Proyek: {$project->category}\n"
                . "- Tahap Proyek: {$project->current_stage}\n"
                . "- Instruksi User: {$promptText}";

            $res = $aiService->chat([
                'system' => $systemInstruction,
                'message' => $projectContext,
                'temperature' => 0.7
            ]);

            // Parse response
            $cleanJson = trim($res->content);
            if (strpos($cleanJson, '```json') !== false) {
                $cleanJson = str_replace(['```json', '```'], '', $cleanJson);
                $cleanJson = trim($cleanJson);
            }

            $data = json_decode($cleanJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON format from AI response: " . $res->content);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal merancang agenda: ' . $e->getMessage()
            ], 500);
        }
    }
}