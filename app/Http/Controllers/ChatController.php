<?php

namespace App\Http\Controllers;

use App\Models\ChatArchive;
use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Tampilan Utama WhatsApp Chat Center
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $showArchived = $request->query('filter') === 'archived';

        // Get archived ids for the user
        $archivedUserIds = ChatArchive::where('user_id', $userId)
            ->whereNotNull('archived_user_id')
            ->pluck('archived_user_id')
            ->toArray();

        $archivedProjectIds = ChatArchive::where('user_id', $userId)
            ->whereNotNull('archived_project_id')
            ->pluck('archived_project_id')
            ->toArray();

        $archivedChatsCount = count($archivedUserIds) + count($archivedProjectIds);

        // 1. Ambil list personal contacts (semua user kecuali diri sendiri)
        $contactsQuery = User::where('id', '!=', $userId);
        if (session()->has('demo_track_id')) {
            $contactsQuery->whereIn('email', ['management@gmail.com', 'user@gmail.com']);
        }

        if ($showArchived) {
            $contactsQuery->whereIn('id', $archivedUserIds);
        } else {
            $contactsQuery->whereNotIn('id', $archivedUserIds);
        }
        $contacts = $contactsQuery->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'role', 'position', 'avatar']);

        // 2. Ambil list project group (semua proyek yang aktif/belum diarsip)
        $groupsQuery = Project::where('is_archived', false);
        if ($showArchived) {
            $groupsQuery->whereIn('id', $archivedProjectIds);
        } else {
            $groupsQuery->whereNotIn('id', $archivedProjectIds);
        }
        $groups = $groupsQuery->orderBy('name', 'asc')
            ->get(['id', 'name', 'category', 'status']);

        // 3. Tentukan chat aktif
        $activeUser = null;
        $activeProject = null;
        $messages = collect();
        $isActiveChatArchived = false;

        if ($request->filled('user_id')) {
            $activeUser = User::findOrFail($request->user_id);
            $isActiveChatArchived = in_array($activeUser->id, $archivedUserIds);

            // Tandai sudah dibaca
            ProjectMessage::where('sender_id', $activeUser->id)
                ->where('recipient_id', $userId)
                ->update(['is_read' => true]);

            // Ambil histori chat personal
            $messages = ProjectMessage::with(['sender', 'task.project'])
                ->where(function ($q) use ($userId, $activeUser) {
                    $q->where('sender_id', $userId)->where('recipient_id', $activeUser->id);
                })
                ->orWhere(function ($q) use ($userId, $activeUser) {
                    $q->where('sender_id', $activeUser->id)->where('recipient_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

        } elseif ($request->filled('project_id')) {
            $activeProject = Project::findOrFail($request->project_id);
            $isActiveChatArchived = in_array($activeProject->id, $archivedProjectIds);

            // Ambil histori chat group project
            $messages = ProjectMessage::with(['sender', 'task.project'])
                ->where('project_id', $activeProject->id)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Ambil daftar tugas yang bisa ditarik
        $availableTasks = collect();
        if ($activeUser) {
            $availableTasks = ProjectTask::with('project')->latest()->get();
        } elseif ($activeProject) {
            $availableTasks = ProjectTask::where('project_id', $activeProject->id)->latest()->get();
        }

        return view('chat.index', compact(
            'contacts',
            'groups',
            'activeUser',
            'activeProject',
            'messages',
            'availableTasks',
            'showArchived',
            'archivedChatsCount',
            'isActiveChatArchived'
        ));
    }

    /**
     * Simpan / Kirim Pesan Baru
     */
    public function send(Request $request): JsonResponse
    {
        if (session()->has('demo_track_id')) {
            return response()->json(['success' => false, 'error' => 'Fitur kirim chat dinonaktifkan di akun demo.'], 403);
        }

        $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'recipient_id' => ['nullable', 'exists:users,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'task_id' => ['nullable', 'exists:project_tasks,id'],
            'file' => ['nullable', 'file', 'max:10240'], // Max 10MB
        ]);

        if (empty($request->message) && ! $request->hasFile('file') && empty($request->task_id)) {
            return response()->json(['success' => false, 'error' => 'Pesan, file, atau referensi tugas tidak boleh kosong.'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $attachmentPath = $file->store('chat_attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $msg = ProjectMessage::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'project_id' => $request->project_id,
            'task_id' => $request->task_id,
            'message' => $request->message,
            'attachment_file' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
        ]);

        // Reload task relationship
        if ($msg->task_id) {
            $msg->load('task.project');
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => Auth::user()->name,
                'sender_avatar' => Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : null,
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_file ? asset('storage/'.$msg->attachment_file) : null,
                'attachment_name' => $msg->attachment_name,
                'created_at' => $msg->created_at->format('H:i'),
                'is_read' => (bool) $msg->is_read,
                'task' => $msg->task ? [
                    'id' => $msg->task->id,
                    'title' => $msg->task->title,
                    'status' => $msg->task->status,
                    'priority' => $msg->task->priority,
                    'project_name' => $msg->task->project->name ?? 'Proyek',
                ] : null,
            ],
        ]);
    }

    /**
     * Ambil Pesan Baru (AJAX Polling Endpoint)
     */
    public function fetchMessages(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $lastId = intval($request->query('last_id', 0));

        $messagesQuery = ProjectMessage::with(['sender', 'task.project'])
            ->where('id', '>', $lastId);

        if ($request->filled('user_id')) {
            $activeUserId = $request->user_id;
            $messagesQuery->where(function ($q) use ($userId, $activeUserId) {
                $q->where('sender_id', $userId)->where('recipient_id', $activeUserId);
            })->orWhere(function ($q) use ($userId, $activeUserId) {
                $q->where('sender_id', $activeUserId)->where('recipient_id', $userId);
            });

            // Tandai pesan masuk yang baru terbaca
            ProjectMessage::where('sender_id', $activeUserId)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

        } elseif ($request->filled('project_id')) {
            $messagesQuery->where('project_id', $request->project_id);
        } else {
            return response()->json(['messages' => []]);
        }

        $newMessages = $messagesQuery->orderBy('created_at', 'asc')->get();

        $formatted = $newMessages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'sender_avatar' => $msg->sender->avatar ? asset('storage/'.$msg->sender->avatar) : null,
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_file ? asset('storage/'.$msg->attachment_file) : null,
                'attachment_name' => $msg->attachment_name,
                'created_at' => $msg->created_at->format('H:i'),
                'is_read' => (bool) $msg->is_read,
                'task' => $msg->task ? [
                    'id' => $msg->task->id,
                    'title' => $msg->task->title,
                    'status' => $msg->task->status,
                    'priority' => $msg->task->priority,
                    'project_name' => $msg->task->project->name ?? 'Proyek',
                ] : null,
            ];
        });

        return response()->json(['messages' => $formatted]);
    }

    /**
     * Archive/Unarchive a chat (Direct User or Project Group)
     */
    public function toggleArchive(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $targetUserId = $request->input('user_id');
        $targetProjectId = $request->input('project_id');

        if ($targetUserId) {
            $existing = ChatArchive::where('user_id', $userId)
                ->where('archived_user_id', $targetUserId)
                ->first();

            if ($existing) {
                $existing->delete();

                return back()->with('success', 'Chat berhasil dipulihkan dari arsip.');
            } else {
                ChatArchive::create([
                    'user_id' => $userId,
                    'archived_user_id' => $targetUserId,
                ]);

                return redirect()->route('chat.index')->with('success', 'Chat berhasil diarsipkan.');
            }
        }

        if ($targetProjectId) {
            $existing = ChatArchive::where('user_id', $userId)
                ->where('archived_project_id', $targetProjectId)
                ->first();

            if ($existing) {
                $existing->delete();

                return back()->with('success', 'Grup berhasil dipulihkan dari arsip.');
            } else {
                ChatArchive::create([
                    'user_id' => $userId,
                    'archived_project_id' => $targetProjectId,
                ]);

                return redirect()->route('chat.index')->with('success', 'Grup berhasil diarsipkan.');
            }
        }

        return back()->with('error', 'Target chat tidak valid.');
    }
}
