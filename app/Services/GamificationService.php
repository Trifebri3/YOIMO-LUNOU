<?php

namespace App\Services;

use App\Models\UserPoint;
use App\Models\UserPointLog;
use App\Models\UserAward;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

        // 2. Catat Log Transaksi
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
                $desc = "Login harian (Streak hari ke-" . $up->login_streak . ")";
                
                // Bonus kelipatan 5 hari streak
                if ($up->login_streak % 5 === 0) {
                    $points += 50;
                    $desc .= " + Bonus Streak 5 Hari!";
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
            // diffInDays === 0 artinya hari yang sama, skip biar ga double claim points
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

        if (!$exists) {
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
     * Evaluasi tugas untuk award "Si Paling Tepat Waktu"
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
    }
}
