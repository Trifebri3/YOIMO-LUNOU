<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\UserPointLog;
use Illuminate\View\View;

class PublicPortfolioController extends Controller
{
    /**
     * Tampilkan halaman portofolio publik pengguna
     */
    public function show(User $user): View
    {
        // 1. Eager load relasi gamifikasi
        $user->load([
            'userPoint',
            'awards',
            'companies',
            'company',
        ]);

        // 2. Ambil semua proyek yang diikuti pengguna (team_matrix, creator, atau perusahaan yang terafiliasi)
        $userCompanyIds = $user->allCompanies()->pluck('id')->toArray();

        $projects = Project::where('is_archived', false)
            ->where(function ($query) use ($user, $userCompanyIds) {
                $query->whereJsonContains('team_matrix', ['user_id' => (string) $user->id])
                    ->orWhereJsonContains('team_matrix', ['user_id' => (int) $user->id])
                    ->orWhere('team_matrix', 'like', '%"user_id":'.$user->id.'%')
                    ->orWhere('team_matrix', 'like', '%"user_id":"'.$user->id.'"%')
                    ->orWhere('created_by', $user->id);

                if (! empty($userCompanyIds)) {
                    $query->orWhereIn('company_profile_id', $userCompanyIds);
                }
            })
            ->with(['company', 'tasks'])
            ->latest()
            ->get();

        // 3. Ambil daftar tugas yang diselesaikan oleh user (Verified Deliverables)
        $completedTasks = ProjectTask::where('assigned_to', $user->id)
            ->where('status', 'Completed')
            ->with(['project.company'])
            ->latest('updated_at')
            ->take(20)
            ->get();

        // 4. Hitung metrik kredibilitas & gamifikasi
        $totalCompleted = ProjectTask::where('assigned_to', $user->id)
            ->where('status', 'Completed')
            ->count();

        $onTimeCount = UserPointLog::where('user_id', $user->id)
            ->where('source_type', 'task_ontime')
            ->count();

        $onTimeRate = $totalCompleted > 0
            ? min(100, (int) round(($onTimeCount / $totalCompleted) * 100))
            : 100;

        $stats = [
            'total_xp' => $user->userPoint->total_points ?? 0,
            'level' => $user->userPoint->level ?? 1,
            'streak' => $user->userPoint->login_streak ?? 0,
            'projects_count' => $projects->count(),
            'completed_tasks_count' => $totalCompleted,
            'ontime_count' => $onTimeCount,
            'ontime_rate' => $onTimeRate,
            'awards_count' => $user->awards->count(),
        ];

        // 5. Ambil log tracing terbaru untuk timeline rekam jejak
        $recentLogs = UserPointLog::where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        return view('public.portfolio.show', compact('user', 'projects', 'completedTasks', 'stats', 'recentLogs'));
    }
}
