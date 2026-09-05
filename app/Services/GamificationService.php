<?php

namespace App\Services;

use App\Models\ProjectTask;
use App\Models\UserAward;
use App\Models\UserPoint;
use App\Models\UserPointLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GamificationService
{
    /**
     * Tambah atau kurangi poin user
     */
    public static function addPoints($userId, $points, $sourceType, $sourceId = null, $companyId = null, $projectId = null, $description = null)
    {
        // 1. Dapatkan atau buat data UserPoint
        $up = UserPoint::firstOrCreate(
            ['user_id' => $userId],
            ['total_points' => 0, 'level' => 1, 'login_streak' => 0]
        );

        $up->total_points += $points;
        if ($up->total_points < 0) {
            $up->total_points = 0;
        }

        // Kalkulasi Level: 1 level per 200 XP
        $up->level = max(1, floor($up->total_points / 200) + 1);
        $up->save();

        // 2. Catat Log Transaksi Tracing
        UserPointLog::create([
            'user_id' => $userId,
            'points' => $points,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'company_profile_id' => $companyId,
            'project_id' => $projectId,
            'description' => $description ?? ucfirst(str_replace('_', ' ', $sourceType)),
        ]);

        return $up;
    }

    /**
     * Berikan poin reward dan tracing saat tugas diselesaikan (Completed / Approved)
     */
    public static function awardTaskCompletion(ProjectTask $task, ?int $userId = null)
    {
        $targetUserId = $userId ?? $task->assigned_to;
        if (! $targetUserId) {
            return null;
        }

        // Cek deduplikasi: jangan beri poin tugas selesai berulang kali untuk tugas yang sama
        $alreadyCompleted = UserPointLog::where('user_id', $targetUserId)
            ->where('source_type', 'task_completed')
            ->where('source_id', $task->id)
            ->exists();

        if ($alreadyCompleted) {
            return null;
        }

        $companyId = $task->project ? $task->project->company_profile_id : null;
        $projectId = $task->project_id;

        // Base XP penyelesaian tugas: +50 XP
        $basePoints = 50;

        // Bonus prioritas
        $priorityBonus = match ($task->priority) {
            'Urgent' => 30,
            'High' => 20,
            'Medium' => 10,
            default => 0,
        };

        $totalCompletionPoints = $basePoints + $priorityBonus;

        $desc = "Menyelesaikan tugas: {$task->title}";
        if ($priorityBonus > 0) {
            $desc .= " (+Bonus Prioritas {$task->priority})";
        }

        // Catat poin dan log tracing penyelesaian tugas
        $userPoint = self::addPoints(
            $targetUserId,
            $totalCompletionPoints,
            'task_completed',
            $task->id,
            $companyId,
            $projectId,
            $desc
        );

        // Cek Ketepatan Waktu: jika diselesaikan pada atau sebelum due_date
        $isOnTime = true;
        if ($task->due_date) {
            $dueDateEnd = Carbon::parse($task->due_date)->endOfDay();
            $isOnTime = now()->lte($dueDateEnd);
        }

        if ($isOnTime) {
            self::addPoints(
                $targetUserId,
                30,
                'task_ontime',
                $task->id,
                $companyId,
                $projectId,
                "Bonus tepat waktu: {$task->title}"
            );
        }

        // Evaluasi Milestone Badge
        self::checkTaskMilestones($targetUserId);

        return $userPoint;
    }

    /**
     * Berikan poin saat pegawai mengirimkan laporan akhir (100% submitFinal)
     */
    public static function awardTaskSubmission(ProjectTask $task, int $userId)
    {
        // Cek deduplikasi
        $alreadySubmitted = UserPointLog::where('user_id', $userId)
            ->where('source_type', 'task_submitted')
            ->where('source_id', $task->id)
            ->exists();

        if ($alreadySubmitted) {
            return null;
        }

        $companyId = $task->project ? $task->project->company_profile_id : null;
        $projectId = $task->project_id;

        return self::addPoints(
            $userId,
            25,
            'task_submitted',
            $task->id,
            $companyId,
            $projectId,
            "Mengirimkan hasil kerja tugas: {$task->title}"
        );
    }

    /**
     * Cek dan berikan poin login harian
     */
    public static function checkDailyLogin($userId)
    {
        $up = UserPoint::firstOrCreate(
            ['user_id' => $userId],
            ['total_points' => 0, 'level' => 1, 'login_streak' => 0]
        );

        $today = Carbon::today();

        if (empty($up->last_login_at)) {
            // Pertama kali login
            $up->login_streak = 1;
            $up->last_login_at = Carbon::now();
            $up->save();

            self::addPoints($userId, 10, 'daily_login', null, null, null, 'Login pertama kali di platform');
        } else {
            $lastLoginDate = Carbon::parse($up->last_login_at)->startOfDay();
            $diffInDays = $today->diffInDays($lastLoginDate);

            if ($diffInDays === 1) {
                // Beruntun (kemarin login)
                $up->login_streak += 1;
                $up->last_login_at = Carbon::now();
                $up->save();

                $points = 10;
                $desc = 'Login harian (Streak hari ke-'.$up->login_streak.')';

                // Bonus kelipatan 5 hari streak
                if ($up->login_streak % 5 === 0) {
                    $points += 50;
                    $desc .= ' + Bonus Streak 5 Hari!';
                }

                self::addPoints($userId, $points, 'daily_login', null, null, null, $desc);

                // Cek Milestone Badge: Si Paling Rajin Login (Minimal 5 hari beruntun)
                if ($up->login_streak >= 5) {
                    self::awardBadge($userId, 'si_paling_rajin_login', 'Si Paling Rajin Login');
                }
            } elseif ($diffInDays > 1) {
                // Putus streak
                $up->login_streak = 1;
                $up->last_login_at = Carbon::now();
                $up->save();

                self::addPoints($userId, 10, 'daily_login', null, null, null, 'Login harian (Streak direset)');
            }
        }
    }

    /**
     * Berikan Badge/Award jika belum punya
     */
    public static function awardBadge($userId, $awardType, $title)
    {
        $exists = UserAward::where('user_id', $userId)
            ->where('award_type', $awardType)
            ->exists();

        if (! $exists) {
            return UserAward::create([
                'user_id' => $userId,
                'award_type' => $awardType,
                'title' => $title,
                'issued_date' => Carbon::now(),
                'share_token' => Str::random(12),
            ]);
        }

        return null;
    }

    /**
     * Evaluasi tugas untuk award dan milestone badges
     */
    public static function checkTaskMilestones($userId)
    {
        // 1. Cek Si Paling Tepat Waktu: menyelesaikan minimal 3 tugas tepat waktu
        $onTimeCount = UserPointLog::where('user_id', $userId)
            ->where('source_type', 'task_ontime')
            ->count();

        if ($onTimeCount >= 3) {
            self::awardBadge($userId, 'si_paling_tepat_waktu', 'Si Paling Tepat Waktu');
        }

        // 2. Cek Si Paling Produktif: total poin terkumpul >= 500
        $up = UserPoint::where('user_id', $userId)->first();
        if ($up && $up->total_points >= 500) {
            self::awardBadge($userId, 'si_paling_produktif', 'Si Paling Produktif');
        }

        // 3. Cek Master Penuntasan Tugas: menyelesaikan minimal 5 tugas
        $completedTaskCount = UserPointLog::where('user_id', $userId)
            ->where('source_type', 'task_completed')
            ->count();

        if ($completedTaskCount >= 5) {
            self::awardBadge($userId, 'master_tugas', 'Master Penuntasan Tugas');
        }

        // 4. Cek Kolaborator Handal: menyelesaikan tugas di lebih dari 1 project
        $distinctProjects = UserPointLog::where('user_id', $userId)
            ->whereNotNull('project_id')
            ->distinct('project_id')
            ->count('project_id');

        if ($distinctProjects >= 2) {
            self::awardBadge($userId, 'kolaborator_handal', 'Kolaborator Handal');
        }
    }
}
