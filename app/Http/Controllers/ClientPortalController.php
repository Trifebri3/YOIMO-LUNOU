<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectAssetRequirement;
use App\Models\ProjectMessage;
use App\Services\AIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        // 4. Get Project Messages & Discussion (Synchronized with Chat Center)
        $messages = ProjectMessage::with(['sender', 'task.project'])
            ->where('project_id', $project->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // 5. Get Client Questions (Legacy support)
        $questions = DB::table('project_client_questions')
            ->where('project_id', $project->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 6. Get Project Asset Requirements (Formulir Pengumpulan Aset & Berkas)
        $assetRequirements = $project->assetRequirements()->orderBy('sort_order')->orderBy('id')->get();
        if ($assetRequirements->isEmpty()) {
            $defaultRequirements = [
                [
                    'title' => 'Logo Perusahaan / Vektor Asli',
                    'description' => 'File logo berformat AI, EPS, SVG, atau PNG resolusi tinggi transparan.',
                    'category' => 'Branding',
                    'is_mandatory' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Pedoman Visual & Brand Guideline',
                    'description' => 'Dokumen PDF atau file panduan warna korporat (hex codes) dan font resmi jika ada.',
                    'category' => 'Branding',
                    'is_mandatory' => false,
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Materi Konten Teks & Gambar Produk',
                    'description' => 'Folder Google Drive, dokumen draft teks halaman (Tentang Kami, Layanan), atau katalog produk.',
                    'category' => 'Konten',
                    'is_mandatory' => true,
                    'sort_order' => 3,
                ],
                [
                    'title' => 'Akses Akun Domain & Web Hosting / Server',
                    'description' => 'Detail login cPanel, Cloudflare, Namecheap, atau penyedia hosting untuk deployment.',
                    'category' => 'Teknis',
                    'is_mandatory' => false,
                    'sort_order' => 4,
                ],
            ];
            foreach ($defaultRequirements as $item) {
                $project->assetRequirements()->create($item);
            }
            $assetRequirements = $project->assetRequirements()->orderBy('sort_order')->orderBy('id')->get();
        }

        $totalAssets = $assetRequirements->count();
        $submittedAssets = $assetRequirements->whereIn('status', ['submitted', 'approved'])->count();
        $assetProgress = $totalAssets > 0 ? round(($submittedAssets / $totalAssets) * 100) : 0;

        // 7. Get AI Executive Project Report (Cached for 1 hour)
        $aiReport = Cache::remember('client_project_report_'.$project->id, 3600, function () use ($project, $roadmaps, $tasks) {
            return $this->generateProjectAiReport($project, $roadmaps, $tasks);
        });

        return view('client.portal', compact('project', 'roadmaps', 'tasks', 'progress', 'questions', 'messages', 'aiReport', 'assetRequirements', 'totalAssets', 'submittedAssets', 'assetProgress'));
    }

    /**
     * Kirim Pesan / Kendala / Dokumen / Screenshot di Portal Klien
     * (100% Sinkron dengan WhatsApp Chat Center Proyek)
     */
    public function sendMessage(Request $request, $token)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'message_type' => ['nullable', 'string', 'in:chat,kendala,question'],
            'client_name' => [Auth::check() ? 'nullable' : 'required', 'string', 'max:100'],
            'file' => ['nullable', 'file', 'max:20480'], // max 20MB
        ]);

        if (empty($request->message) && ! $request->hasFile('file')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Pesan atau lampiran tidak boleh kosong.'], 422);
            }

            return back()->with('error', 'Pesan atau lampiran tidak boleh kosong.');
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $attachmentPath = $file->store('chat_attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $senderId = Auth::id();
        $clientName = Auth::check() ? Auth::user()->name : trim($request->client_name);

        $message = ProjectMessage::create([
            'project_id' => $project->id,
            'sender_id' => $senderId,
            'client_name' => $senderId ? null : $clientName,
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'chat',
            'attachment_file' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
        ]);

        // Legacy compatibility: simpan ke project_client_questions jika bertipe question
        if ($message->message_type === 'question' && ! empty($request->message)) {
            DB::table('project_client_questions')->insert([
                'project_id' => $project->id,
                'client_name' => $clientName,
                'question' => trim($request->message),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender_display_name,
                    'sender_role' => $message->sender_display_role,
                    'sender_avatar' => $message->sender_display_avatar,
                    'is_me' => Auth::check() && $message->sender_id === Auth::id(),
                    'is_team' => (bool) $message->sender_id,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'is_resolved' => (bool) $message->is_resolved,
                    'resolved_at' => $message->resolved_at ? $message->resolved_at->diffForHumans() : null,
                    'resolver_name' => $message->resolver_name,
                    'resolution_note' => $message->resolution_note,
                    'attachment_url' => $message->attachment_file ? asset('storage/'.$message->attachment_file) : null,
                    'attachment_name' => $message->attachment_name,
                    'is_image' => $message->is_image,
                    'created_at' => $message->created_at->diffForHumans(),
                    'created_time' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Pesan Anda berhasil dikirim ke tim proyek.');
    }

    /**
     * Ambil Pesan Baru (AJAX Polling Realtime untuk Portal Klien)
     */
    public function fetchMessages(Request $request, $token)
    {
        $project = Project::where('share_token', $token)->firstOrFail();
        $lastId = intval($request->query('last_id', 0));

        $newMessages = ProjectMessage::with('sender')
            ->where('project_id', $project->id)
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = $newMessages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender_display_name,
                'sender_role' => $msg->sender_display_role,
                'sender_avatar' => $msg->sender_display_avatar,
                'is_me' => Auth::check() && $msg->sender_id === Auth::id(),
                'is_team' => (bool) $msg->sender_id,
                'message' => $msg->message,
                'message_type' => $msg->message_type ?? 'chat',
                'is_resolved' => (bool) $msg->is_resolved,
                'resolved_at' => $msg->resolved_at ? $msg->resolved_at->diffForHumans() : null,
                'resolver_name' => $msg->resolver_name,
                'resolution_note' => $msg->resolution_note,
                'attachment_url' => $msg->attachment_file ? asset('storage/'.$msg->attachment_file) : null,
                'attachment_name' => $msg->attachment_name,
                'is_image' => $msg->is_image,
                'created_at' => $msg->created_at->diffForHumans(),
                'created_time' => $msg->created_at->format('H:i'),
            ];
        });

        return response()->json(['messages' => $formatted]);
    }

    /**
     * Toggle Status Penyelesaian Hambatan / Kendala di Portal Klien
     */
    public function toggleResolution(Request $request, $token, ProjectMessage $message)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        if ($message->project_id !== $project->id) {
            return response()->json(['success' => false, 'error' => 'Pesan tidak ditemukan pada proyek ini.'], 404);
        }

        $request->validate([
            'is_resolved' => ['required', 'boolean'],
            'resolution_note' => ['nullable', 'string', 'max:1000'],
            'resolver_name' => ['nullable', 'string', 'max:100'],
        ]);

        $isResolved = $request->boolean('is_resolved');

        $resolverName = Auth::check()
            ? Auth::user()->name
            : (trim($request->resolver_name) ?: 'Klien');

        $message->update([
            'is_resolved' => $isResolved,
            'resolved_at' => $isResolved ? Carbon::now() : null,
            'resolved_by' => $isResolved ? Auth::id() : null,
            'resolver_name' => $isResolved ? $resolverName : null,
            'resolution_note' => $isResolved ? trim($request->resolution_note) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isResolved ? 'Hambatan berhasil ditandai sebagai terselesaikan.' : 'Hambatan dibuka kembali.',
            'data' => [
                'id' => $message->id,
                'is_resolved' => (bool) $message->is_resolved,
                'resolved_at' => $message->resolved_at ? $message->resolved_at->diffForHumans() : null,
                'resolver_name' => $message->resolver_name,
                'resolution_note' => $message->resolution_note,
            ],
        ]);
    }

    /**
     * Kirim Pertanyaan Klien (Legacy method tetap ada dan juga sinkron ke project_messages)
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

        // Sinkronisasi ke project_messages juga
        ProjectMessage::create([
            'project_id' => $project->id,
            'sender_id' => Auth::id(),
            'client_name' => Auth::check() ? null : trim($request->client_name),
            'message' => trim($request->question),
            'message_type' => 'question',
            'is_read' => false,
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

    /**
     * Submit Asset / Berkas dari Klien
     */
    public function submitAssetRequirement(Request $request, $token, ProjectAssetRequirement $asset)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        if ($asset->project_id !== $project->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Permintaan aset tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Permintaan aset tidak ditemukan.');
        }

        $request->validate([
            'client_name' => [Auth::check() ? 'nullable' : 'required', 'string', 'max:100'],
            'external_url' => ['nullable', 'url', 'max:1000'],
            'client_notes' => ['nullable', 'string', 'max:3000'],
            'file' => ['nullable', 'file', 'max:51200'], // max 50MB
        ]);

        if (! $request->hasFile('file') && empty($request->external_url) && empty($request->client_notes)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Harap unggah file, masukkan link, atau tulis catatan berkas.'], 422);
            }

            return back()->with('error', 'Harap unggah file, masukkan link, atau tulis catatan berkas.');
        }

        $clientName = Auth::check() ? Auth::user()->name : trim($request->client_name);

        $updateData = [
            'status' => 'submitted',
            'submitted_by_name' => $clientName,
            'submitted_at' => Carbon::now(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $updateData['file_path'] = $file->store('project_assets', 'public');
            $updateData['file_name'] = $file->getClientOriginalName();
            $updateData['file_size'] = $file->getSize();
        }

        if ($request->filled('external_url')) {
            $updateData['external_url'] = trim($request->external_url);
        }

        if ($request->filled('client_notes')) {
            $updateData['client_notes'] = trim($request->client_notes);
        }

        $asset->update($updateData);

        // Kirim notifikasi sinkron ke obrolan proyek
        $msgContent = "📎 [Pengumpulan Aset] {$clientName} mengunggah berkas untuk: *{$asset->title}*";
        if ($asset->external_url) {
            $msgContent .= "\nTautan: {$asset->external_url}";
        }
        if ($asset->client_notes) {
            $msgContent .= "\nCatatan: {$asset->client_notes}";
        }

        ProjectMessage::create([
            'project_id' => $project->id,
            'sender_id' => Auth::id(),
            'client_name' => Auth::check() ? null : $clientName,
            'message' => $msgContent,
            'message_type' => 'chat',
            'attachment_file' => $asset->file_path,
            'attachment_name' => $asset->file_name,
            'is_read' => false,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $totalAssets = $project->assetRequirements()->count();
            $submittedAssets = $project->assetRequirements()->whereIn('status', ['submitted', 'approved'])->count();
            $progress = $totalAssets > 0 ? round(($submittedAssets / $totalAssets) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => "Aset '{$asset->title}' berhasil dikirimkan!",
                'asset' => $asset->fresh(),
                'metrics' => [
                    'total' => $totalAssets,
                    'submitted' => $submittedAssets,
                    'progress' => $progress,
                ],
            ]);
        }

        return back()->with('success', "Aset '{$asset->title}' berhasil dikirimkan!");
    }

    /**
     * Tambah Permintaan Aset Baru (Bisa disesuaikan per kebutuhan proyek)
     */
    public function storeAssetRequirement(Request $request, $token)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_mandatory' => ['nullable', 'boolean'],
        ]);

        $newAsset = $project->assetRequirements()->create([
            'title' => trim($request->title),
            'description' => trim($request->description),
            'category' => trim($request->category) ?: 'Umum',
            'is_mandatory' => $request->boolean('is_mandatory'),
            'status' => 'pending',
            'sort_order' => ($project->assetRequirements()->max('sort_order') ?? 0) + 1,
            'created_by' => Auth::id(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $totalAssets = $project->assetRequirements()->count();
            $submittedAssets = $project->assetRequirements()->whereIn('status', ['submitted', 'approved'])->count();
            $progress = $totalAssets > 0 ? round(($submittedAssets / $totalAssets) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Permintaan aset baru berhasil ditambahkan.',
                'asset' => $newAsset,
                'metrics' => [
                    'total' => $totalAssets,
                    'submitted' => $submittedAssets,
                    'progress' => $progress,
                ],
            ]);
        }

        return back()->with('success', 'Permintaan aset baru berhasil ditambahkan.');
    }

    /**
     * Hapus Permintaan Aset
     */
    public function deleteAssetRequirement(Request $request, $token, ProjectAssetRequirement $asset)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        if ($asset->project_id !== $project->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Aset tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Aset tidak ditemukan.');
        }

        $title = $asset->title;
        if ($asset->file_path && Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }
        $asset->delete();

        if ($request->expectsJson() || $request->ajax()) {
            $totalAssets = $project->assetRequirements()->count();
            $submittedAssets = $project->assetRequirements()->whereIn('status', ['submitted', 'approved'])->count();
            $progress = $totalAssets > 0 ? round(($submittedAssets / $totalAssets) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => "Permintaan aset '{$title}' telah dihapus.",
                'metrics' => [
                    'total' => $totalAssets,
                    'submitted' => $submittedAssets,
                    'progress' => $progress,
                ],
            ]);
        }

        return back()->with('success', "Permintaan aset '{$title}' telah dihapus.");
    }

    /**
     * Toggle Approval Aset oleh Tim / Klien
     */
    public function toggleAssetApproval(Request $request, $token, ProjectAssetRequirement $asset)
    {
        $project = Project::where('share_token', $token)->firstOrFail();

        if ($asset->project_id !== $project->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Aset tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Aset tidak ditemukan.');
        }

        $isCurrentlyApproved = ($asset->status === 'approved');
        $newStatus = $isCurrentlyApproved
            ? ($asset->file_path || $asset->external_url || $asset->client_notes ? 'submitted' : 'pending')
            : 'approved';

        $asset->update([
            'status' => $newStatus,
            'reviewed_by' => $newStatus === 'approved' ? Auth::id() : null,
            'reviewed_at' => $newStatus === 'approved' ? Carbon::now() : null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $newStatus === 'approved' ? 'Aset disetujui!' : 'Status persetujuan dibatalkan.',
                'asset' => $asset->fresh(),
            ]);
        }

        return back()->with('success', $newStatus === 'approved' ? 'Aset disetujui!' : 'Status persetujuan dibatalkan.');
    }
}
