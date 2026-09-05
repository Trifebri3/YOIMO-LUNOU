<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\ProjectTask;
use App\Models\UserPoint;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['user.*', 'management.*', 'layouts.*'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // 1. Companies for User (Multi-company support via pivot, legacy FK, and manager)
                $userCompanies = $user->allCompanies();
                $userCompanyIds = $userCompanies->pluck('id')->toArray();

                // 2. Projects for User (in team_matrix, creator, or member of the project company)
                $navProjects = Project::where('is_archived', false)
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
                    ->with('company')
                    ->latest()
                    ->get();

                $projectCompanies = $navProjects->pluck('company')->filter();
                $navCompanies = $userCompanies->concat($projectCompanies)->filter()->unique('id')->values();

                // 3. Active Company fallback
                $navActiveCompany = $view->offsetExists('company')
                    ? $view->offsetGet('company')
                    : ($view->offsetExists('project') && $view->offsetGet('project')?->company
                        ? $view->offsetGet('project')->company
                        : $navCompanies->first());

                // 4. Pending Tasks Count
                $navPendingTasksCount = ProjectTask::where('assigned_to', $user->id)
                    ->whereIn('status', ['Todo', 'In Progress'])
                    ->count();

                // 5. Unread Chat Count
                $navUnreadChatCount = ProjectMessage::where('recipient_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                // 6. User Point / Streak
                $navUserPoint = UserPoint::firstOrCreate(
                    ['user_id' => $user->id],
                    ['total_points' => 0, 'level' => 1, 'login_streak' => 0]
                );

                $view->with([
                    'navUser' => $user,
                    'navProjects' => $navProjects,
                    'navCompanies' => $navCompanies,
                    'navActiveCompany' => $navActiveCompany,
                    'navPendingTasksCount' => $navPendingTasksCount,
                    'navUnreadChatCount' => $navUnreadChatCount,
                    'navUserPoint' => $navUserPoint,
                ]);
            }
        });
    }
}
