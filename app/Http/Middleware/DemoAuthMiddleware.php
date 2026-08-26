<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DemoAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Throttled cleanup of demo activities older than 3 hours
        if (!\Illuminate\Support\Facades\Cache::has('demo_cleanup_cooldown')) {
            \Illuminate\Support\Facades\Cache::put('demo_cleanup_cooldown', true, 300); // 5 minutes cooldown
            
            $threeHoursAgo = \Carbon\Carbon::now()->subHours(3);
            $userIds = [2, 4];
            
            try {
                \Illuminate\Support\Facades\DB::table('wellbeing_checkins')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();
                    
                \Illuminate\Support\Facades\DB::table('wellbeing_journals')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('wellbeing_goals')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('wellbeing_reflections')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('wellbeing_left_thoughts')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('user_point_logs')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('user_awards')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('chatbot_messages')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('project_activity_logs')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('project_tasks')
                    ->whereIn('assigned_to', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();
                    
                \Illuminate\Support\Facades\DB::table('agendas')
                    ->whereIn('user_id', $userIds)
                    ->where('created_at', '<', $threeHoursAgo)
                    ->delete();

                \Illuminate\Support\Facades\DB::table('user_points')
                    ->whereIn('user_id', $userIds)
                    ->where('updated_at', '<', $threeHoursAgo)
                    ->update([
                        'total_points' => 0,
                        'level' => 1,
                        'login_streak' => 0,
                        'updated_at' => \Carbon\Carbon::now()
                    ]);
            } catch (\Exception $e) {
                // Fail silently to avoid interrupting the request flow
                \Illuminate\Support\Facades\Log::error('Demo cleanup error: ' . $e->getMessage());
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
