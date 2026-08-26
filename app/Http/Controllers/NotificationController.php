<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectTask;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications + dynamic deadline alerts
     */
    public function getUnread(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['notifications' => [], 'unread_count' => 0], 401);
        }

        $userId = $user->id;
        $dismissed = session()->get('dismissed_deadlines', []);

        // 1. Fetch DB notifications
        $dbNotifications = $user->unreadNotifications()->take(10)->get();
        $dbUnreadCount = $user->unreadNotifications()->count();

        $notifications = $dbNotifications->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->data['type'] ?? 'general',
                'action_type' => $n->data['action_type'] ?? '',
                'title' => $n->data['title'] ?? 'Notifikasi',
                'message' => $n->data['message'] ?? '',
                'project_id' => $n->data['project_id'] ?? null,
                'task_id' => $n->data['task_id'] ?? null,
                'agenda_id' => $n->data['agenda_id'] ?? null,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        })->toArray();

        // 2. Fetch dynamic task deadline warnings
        $upcomingTasks = ProjectTask::where('assigned_to', $userId)
            ->whereIn('status', ['Todo', 'In Progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', Carbon::now()->addDays(3))
            ->where('due_date', '>=', Carbon::now()->subDays(2))
            ->with('project')
            ->get();

        $activeTaskWarnings = [];
        foreach ($upcomingTasks as $task) {
            $notifId = 'deadline-task-'.$task->id;
            if (in_array($notifId, $dismissed)) {
                continue;
            }

            $daysLeft = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($task->due_date)->startOfDay(), false);
            $dueStr = $daysLeft == 0 ? 'hari ini' : ($daysLeft < 0 ? 'terlewat '.abs($daysLeft).' hari' : "dalam {$daysLeft} hari");

            $activeTaskWarnings[] = [
                'id' => $notifId,
                'type' => 'deadline',
                'action_type' => 'approaching',
                'title' => 'Tenggat Tugas Dekat',
                'message' => "Tugas '{$task->title}' pada proyek ".($task->project->name ?? 'Proyek')." jatuh tempo {$dueStr}.",
                'project_id' => $task->project_id,
                'task_id' => $task->id,
                'agenda_id' => null,
                'created_at' => 'Peringatan',
            ];
        }
        $notifications = array_merge($notifications, $activeTaskWarnings);

        // 3. Fetch dynamic project deadline warnings
        $myCompanyIds = CompanyProfile::where('manager_id', $userId)->pluck('id');
        $upcomingProjects = Project::where('is_archived', false)
            ->whereNotIn('status', ['Completed'])
            ->whereNotNull('deadline')
            ->where('deadline', '<=', Carbon::now()->addDays(5))
            ->where('deadline', '>=', Carbon::now()->subDays(2))
            ->where(function ($query) use ($myCompanyIds, $userId) {
                $query->whereIn('company_profile_id', $myCompanyIds)
                    ->orWhere('created_by', $userId);
            })
            ->get();

        $activeProjectWarnings = [];
        foreach ($upcomingProjects as $proj) {
            $notifId = 'deadline-project-'.$proj->id;
            if (in_array($notifId, $dismissed)) {
                continue;
            }

            $daysLeft = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($proj->deadline)->startOfDay(), false);
            $dueStr = $daysLeft == 0 ? 'hari ini' : ($daysLeft < 0 ? 'terlewat '.abs($daysLeft).' hari' : "dalam {$daysLeft} hari");

            $activeProjectWarnings[] = [
                'id' => $notifId,
                'type' => 'deadline',
                'action_type' => 'approaching',
                'title' => 'Tenggat Proyek Dekat',
                'message' => "Proyek '{$proj->name}' akan segera berakhir {$dueStr}.",
                'project_id' => $proj->id,
                'task_id' => null,
                'agenda_id' => null,
                'created_at' => 'Peringatan',
            ];
        }
        $notifications = array_merge($notifications, $activeProjectWarnings);

        $unreadCount = $dbUnreadCount + count($activeTaskWarnings) + count($activeProjectWarnings);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark specific notification as read
     */
    public function markAsRead(string $id): JsonResponse
    {
        // Dynamic deadline alerts are dismissed via session
        if (str_starts_with($id, 'deadline-')) {
            $dismissed = session()->get('dismissed_deadlines', []);
            $dismissed[] = $id;
            session()->put('dismissed_deadlines', array_unique($dismissed));

            return response()->json(['success' => true]);
        }

        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        // Fetch currently visible active deadlines and mark them dismissed as well
        $userId = Auth::id();
        $dismissed = session()->get('dismissed_deadlines', []);

        $upcomingTasks = ProjectTask::where('assigned_to', $userId)
            ->whereIn('status', ['Todo', 'In Progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', Carbon::now()->addDays(3))
            ->where('due_date', '>=', Carbon::now()->subDays(2))
            ->pluck('id');
        foreach ($upcomingTasks as $tid) {
            $dismissed[] = 'deadline-task-'.$tid;
        }

        $myCompanyIds = CompanyProfile::where('manager_id', $userId)->pluck('id');
        $upcomingProjects = Project::where('is_archived', false)
            ->whereNotIn('status', ['Completed'])
            ->whereNotNull('deadline')
            ->where('deadline', '<=', Carbon::now()->addDays(5))
            ->where('deadline', '>=', Carbon::now()->subDays(2))
            ->where(function ($query) use ($myCompanyIds, $userId) {
                $query->whereIn('company_profile_id', $myCompanyIds)
                    ->orWhere('created_by', $userId);
            })
            ->pluck('id');
        foreach ($upcomingProjects as $pid) {
            $dismissed[] = 'deadline-project-'.$pid;
        }

        session()->put('dismissed_deadlines', array_unique($dismissed));

        return response()->json(['success' => true]);
    }
}
