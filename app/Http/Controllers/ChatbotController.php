<?php

namespace App\Http\Controllers;

use App\Models\ChatbotMessage;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectAgenda;
use App\Models\ProjectTask;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle live query request from floating chatbot mascot
     */
    public function query(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        ChatbotMessage::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $request->message,
        ]);

        $msg = strtolower(trim($request->message));
        $reply = '';
        $emotion = 'happy'; // maps to different icons
        $isCreateAction = preg_match('/\b(buat|tambah|rancang|jadwal|rapat|create|add|schedule|agenda|tugas|task)\b/i', $msg);

        // 1. Greet handler (Skip if asking to create something)
        if (! $isCreateAction && preg_match('/\b(halo|hi|hei|hello|pagi|siang|sore|malam|assalamualaikum|permisi)\b/i', $msg)) {
            $replies = [
                'Halo, '.$user->name.'! LUNOU siap meluncur! Ada yang bisa LUNOU bantu hari ini?',
                'Hei! Senang bertemu denganmu lagi. Yuk kita selesaikan target proyek hari ini!',
                'Halo! Aku LUNOU, maskot workspace-mu. Ada yang ingin kamu diskusikan?',
            ];
            $reply = $replies[array_rand($replies)];
            $emotion = 'excited';

            // 2. Project stats query (Skip if asking to create something)
        } elseif (! $isCreateAction && preg_match('/\b(proyek|project|daftar proyek|company proyek|agenda proyek)\b/i', $msg)) {
            $projects = Project::latest()->take(3)->get(['id', 'name', 'status', 'progress_percentage']);
            if ($projects->isEmpty()) {
                $reply = 'Saat ini belum ada proyek aktif di workspace ini. Butuh bantuan LUNOU untuk buat proyek baru?';
                $emotion = 'idle';
            } else {
                $reply = "Berikut adalah beberapa proyek terbaru yang sedang berjalan:\n";
                foreach ($projects as $p) {
                    $url = '/user/projects/'.$p->id;
                    $reply .= "• Proyek [{$p->name}]({$url}) [{$p->status}] - Progres: {$p->progress_percentage}%\n";
                }
                $reply .= "\nSemuanya terpantau lancar! Semangat tim!";
                $emotion = 'excited';
            }

            // 3. User tasks query (Skip if asking to create something)
        } elseif (! $isCreateAction && preg_match('/\b(tugas|task|pekerjaan saya|tugas saya|pending)\b/i', $msg)) {
            $tasks = ProjectTask::where('assigned_to', $user->id)
                ->where('status', '!=', 'Completed')
                ->latest()
                ->take(3)
                ->get(['title', 'status', 'due_date', 'project_id']);

            if ($tasks->isEmpty()) {
                $reply = 'Luar biasa! Kamu tidak memiliki tugas pending saat ini. Pertahankan performa hebatmu, ya!';
                $emotion = 'happy';
            } else {
                $reply = "Ini daftar tugas aktif yang perlu kamu selesaikan:\n";
                foreach ($tasks as $t) {
                    $dueDate = $t->due_date ? date('d M Y', strtotime($t->due_date)) : 'Tanpa deadline';
                    $url = '/user/projects/'.$t->project_id.'?tab=tasks';
                    $reply .= "• Tugas [{$t->title}]({$url}) (Status: {$t->status}) - Selesai sebelum: {$dueDate}\n";
                }
                $reply .= "\nKamu pasti bisa menyelesaikannya tepat waktu!";
                $emotion = 'excited';
            }

            // 4. Meeting Agendas query (Skip if asking to create something)
        } elseif (! $isCreateAction && preg_match('/\b(agenda|rapat|meeting|jadwal|kalender)\b/i', $msg)) {
            $agendas = ProjectAgenda::latest()->take(3)->get(['title', 'start_date', 'location_address', 'meeting_url']);
            if ($agendas->isEmpty()) {
                $reply = 'Tidak ada jadwal rapat atau agenda terdekat saat ini. Workspace terpantau hening dan fokus!';
                $emotion = 'idle';
            } else {
                $reply = "Berikut jadwal agenda rapat terdekat:\n";
                foreach ($agendas as $a) {
                    $date = date('d M Y H:i', strtotime($a->start_date));
                    $loc = $a->meeting_url ?: ($a->location_address ?: 'Online');
                    $reply .= "• *{$a->title}* - Tanggal: {$date} di {$loc}\n";
                }
                $emotion = 'happy';
            }

            // 5. Encourage / Motivation booster
        } elseif (preg_match('/\b(semangat|motivasi|capek|lelah|sedih|stres|stress|bosan)\b/i', $msg)) {
            $boosters = [
                'Hei, istirahatlah sejenak jika lelah. LUNOU selalu mendukungmu! Ingatlah, setiap langkah kecil membawamu lebih dekat ke peluncuran produk!',
                'Jangan menyerah! Ingat impian besar yang ingin kita capai di workspace ini. Kamu luar biasa!',
                'Tarik napas dalam-dalam, minum air putih, lalu mari kita taklukkan barisan kode ini bersama-sama. LUNOU di sini menemanimu!',
            ];
            $reply = $boosters[array_rand($boosters)];
            $emotion = 'happy';

            // 6. Help / Feature Guide
        } elseif (preg_match('/\b(bantuan|help|fitur|cara kerja|bisa apa)\b/i', $msg)) {
            $reply = "LUNOU bisa membantumu memantau & mengelola workspace dengan cepat! Coba tanyakan hal berikut:\n"
                   ."1. \"Bagaimana status *proyek* terbaru?\"\n"
                   ."2. \"Tampilkan daftar *tugas saya*.\"\n"
                   ."3. \"Apakah ada *jadwal rapat* terdekat?\"\n"
                   ."4. \"Buat tugas Slicing Front End & Buat tugas Core API untuk proyek Yota Yoti.\"\n"
                   ."5. \"Buat agenda meeting Zoom besok jam 10 pagi.\"\n"
                   .'6. Berikan aku *semangat*.';
            $emotion = 'idle';

            // 7. Dynamic LLM Chat fallback (using AIService)
        } else {
            try {
                $aiService = app(AIService::class);

                $projects = Project::latest()->get(['id', 'name']);
                $projectsContext = '';
                foreach ($projects as $p) {
                    $projectsContext .= "- ID: {$p->id}, Nama: {$p->name}\n";
                }

                $systemPrompt = 'Kamu adalah LUNOU, maskot asisten pintar, ramah, dan empati di workspace Yoimo. '
                    ."Jawab pertanyaan pengguna dengan ramah, santai, dan profesional. Nama pengguna: {$user->name}. Hari/Tanggal saat ini: ".date('l, d F Y H:i').".\n\n"
                    ."KEMAMPUAN UTAMA (PEMBUATAN TUGAS & AGENDA OTOMATIS):\n"
                    ."Jika user meminta untuk membuat tugas (misal: 'buat tugas slicing UI dan setup DB') atau membuat agenda/rapat (misal: 'buat agenda rapat besok jam 10'), kamu wajib merancang data tersebut dan mengembalikannya dalam struktur objek JSON.\n\n"
                    ."Daftar Proyek Yoimo yang tersedia:\n{$projectsContext}\n\n"
                    ."Format response Anda WAJIB berupa raw JSON valid (tanpa pembungkus markdown ```json) dengan struktur:\n"
                    ."{\n"
                    ."  \"reply\": \"Pesan ramah Anda memberitahu bahwa Anda telah memproses permintaan pembuatan tugas/agenda.\",\n"
                    ."  \"emotion\": \"excited\",\n"
                    ."  \"actions\": [\n"
                    ."     {\n"
                    ."       \"type\": \"create_task\",\n"
                    ."       \"project_id\": 1, // Cocokkan dengan ID proyek yang sesuai, atau ID proyek pertama/terbaru secara default jika tidak disebutkan\n"
                    ."       \"title\": \"Judul tugas\",\n"
                    ."       \"priority\": \"High\", // 'Low', 'Medium', 'High', 'Urgent'\n"
                    .'       "due_date": "YYYY-MM-DD" // Gunakan tanggal referensi hari ini ('.now()->toDateString().")\n"
                    ."     },\n"
                    ."     {\n"
                    ."       \"type\": \"create_agenda\",\n"
                    ."       \"project_id\": 1,\n"
                    ."       \"title\": \"Judul agenda/rapat\",\n"
                    ."       \"category\": \"Meeting Online (Google Meet / Zoom)\", // 'Meeting Online (Google Meet / Zoom)', 'Meeting Offline (Tatap Muka)', 'Olahraga & Kesehatan', 'Liburan & Outing', 'Nonton & Hiburan', 'Roadshow & Kunjungan', 'Workshop & Pelatihan'\n"
                    ."       \"start_date\": \"YYYY-MM-DD\", // tanggal\n"
                    ."       \"start_time\": \"HH:MM\", // jam\n"
                    ."       \"location_type\": \"online\" // 'online', 'offline'\n"
                    ."     }\n"
                    ."  ]\n"
                    ."}\n\n"
                    .'Bila user hanya menyapa atau mengobrol biasa tanpa meminta membuat tugas/agenda, isi "actions" dengan array kosong []. Jangan berikan markdown tebal berlebihan.';

                $aiResponse = $aiService->chat([
                    'system' => $systemPrompt,
                    'message' => $request->message,
                    'temperature' => 0.7,
                ]);

                $cleanJson = trim($aiResponse->content);
                if (strpos($cleanJson, '```json') !== false) {
                    $cleanJson = str_replace(['```json', '```'], '', $cleanJson);
                    $cleanJson = trim($cleanJson);
                }

                $data = json_decode($cleanJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    $reply = $data['reply'] ?? 'Baik, LUNOU telah laksanakan!';
                    $emotion = $data['emotion'] ?? 'happy';
                    $actions = $data['actions'] ?? [];

                    foreach ($actions as $action) {
                        if (! isset($action['project_id']) || empty($action['project_id'])) {
                            $action['project_id'] = Project::latest()->value('id') ?? 1;
                        }

                        if (($action['type'] ?? '') === 'create_task') {
                            $newTask = ProjectTask::create([
                                'project_id' => $action['project_id'],
                                'title' => $action['title'] ?? 'Tugas Baru',
                                'description' => 'Dibuat otomatis oleh LUNOU AI Chatbot.',
                                'status' => 'Todo',
                                'priority' => $action['priority'] ?? 'Medium',
                                'due_date' => $action['due_date'] ?? null,
                                'assigned_to' => $user->id,
                                'created_by' => $user->id,
                            ]);
                            ProjectActivityLog::record($action['project_id'], 'Task', 'CREATE', "Membuat tugas baru via Chatbot: '{$newTask->title}'");
                        } elseif (($action['type'] ?? '') === 'create_agenda') {
                            $newAgenda = ProjectAgenda::create([
                                'project_id' => $action['project_id'],
                                'title' => $action['title'] ?? 'Agenda Baru',
                                'description' => 'Dijadwalkan otomatis oleh LUNOU AI Chatbot.',
                                'category' => (function () use ($action) {
                                    $cat = $action['category'] ?? 'Meeting Online';
                                    if (strpos($cat, 'Meeting Online') !== false || strpos($cat, 'Zoom') !== false || strpos($cat, 'Meet') !== false) {
                                        return 'Meeting Online';
                                    } elseif (strpos($cat, 'Meeting Offline') !== false || strpos($cat, 'Tatap Muka') !== false) {
                                        return 'Meeting Offline';
                                    } elseif (strpos($cat, 'Olahraga') !== false) {
                                        return 'Olahraga & Kesehatan';
                                    } elseif (strpos($cat, 'Liburan') !== false) {
                                        return 'Liburan & Outing';
                                    } elseif (strpos($cat, 'Nonton') !== false) {
                                        return 'Nonton & Hiburan';
                                    } elseif (strpos($cat, 'Roadshow') !== false) {
                                        return 'Roadshow & Kunjungan';
                                    } elseif (strpos($cat, 'Workshop') !== false) {
                                        return 'Workshop & Pelatihan';
                                    }

                                    return 'Lainnya';
                                })(),
                                'start_date' => $action['start_date'] ?? now()->toDateString(),
                                'start_time' => $action['start_time'] ?? null,
                                'recurrence' => 'once',
                                'location_type' => $action['location_type'] ?? 'online',
                                'created_by' => $user->id,
                                'status' => 'Scheduled',
                                'attendee_ids' => [],
                            ]);
                            ProjectActivityLog::record($action['project_id'], 'Agenda', 'CREATE', "Menjadwalkan agenda baru via Chatbot: '{$newAgenda->title}'");
                        }
                    }
                } else {
                    $reply = $aiResponse->content;
                    $emotion = 'happy';
                }
            } catch (\Exception $e) {
                Log::error('Chatbot AI Exception: '.$e->getMessage()."\n".$e->getTraceAsString());
                // Fallback to static defaults if AI is not configured or fails
                $defaults = [
                    "Hmm, LUNOU mengerti maksudmu, tapi LUNOU butuh info lebih detail. Coba ketik 'bantuan' untuk melihat daftar perintah!",
                    'Pesanmu menarik sekali! Sayangnya modul AI lokal LUNOU masih belajar memahami ini. Ada hal lain yang bisa LUNOU bantu?',
                    'LUNOU berkedip kebingungan... Coba tanyakan tentang proyek, tugas, atau minta kata motivasi!',
                ];
                $reply = $defaults[array_rand($defaults)];
                $emotion = 'sleeping';
            }
        }

        ChatbotMessage::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $reply,
        ]);

        return response()->json([
            'reply' => $reply,
            'emotion' => $emotion,
        ]);
    }
}
