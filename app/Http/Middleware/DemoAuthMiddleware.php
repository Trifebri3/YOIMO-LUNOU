<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DemoAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Throttled cleanup of demo activities older than 3 hours
        if (! Cache::has('demo_cleanup_cooldown')) {
            Cache::put('demo_cleanup_cooldown', true, 300); // 5 minutes cooldown

            $threeHoursAgo = Carbon::now()->subHours(3);
            $userIds = [2, 4];

            try {
                // Delete expired demo tracks (this will cascade delete all associated companies, projects, tasks, etc.)
                DB::table('demo_tracks')
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('wellbeing_checkins')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('wellbeing_journals')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('wellbeing_goals')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('wellbeing_reflections')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('wellbeing_left_thoughts')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('user_point_logs')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('user_awards')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('chatbot_messages')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('project_activity_logs')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('project_tasks')
                    ->whereIn('assigned_to', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('project_agendas')
                    ->whereIn('created_by', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                DB::table('user_points')
                    ->whereIn('user_id', $userIds)
                    ->where('updated_at', '<', $threeHoursAgo)
                    ->update([
                        'total_points' => 0,
                        'level' => 1,
                        'login_streak' => 0,
                        'updated_at' => Carbon::now(),
                    ]);
            } catch (\Exception $e) {
                // Fail silently to avoid interrupting the request flow
                Log::error('Demo cleanup error: '.$e->getMessage());
            }
        }

        // 2. Set dynamic in-memory names for demo session
        if (session()->has('demo_user_role')) {
            $role = session()->get('demo_user_role');

            if (Auth::check()) {
                $user = Auth::user();
                $customName = session()->get('demo_user_name', $role === 'management' ? 'Demo Manager' : 'Demo Employee');
                $customEmail = session()->get('demo_user_email', $role === 'management' ? 'demo_manager@yoimo.com' : 'demo_employee@yoimo.com');

                if ($role === 'management' && $user->role === 'management') {
                    $user->name = $customName;
                    $user->email = $customEmail;
                } elseif ($role === 'user' && $user->role === 'user') {
                    $user->name = $customName;
                    $user->email = $customEmail;
                }
            }
        }

        return $next($request);
    }
}
