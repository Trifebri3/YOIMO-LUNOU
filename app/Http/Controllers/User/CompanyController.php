<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectAgenda;
use App\Models\ProjectDocument;
use App\Models\ProjectExpense;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Dashboard Workspace Company untuk User Biasa
     */
    public function workspace(Request $request, CompanyProfile $company): View
    {
        $userId = Auth::id();

        // Pastikan user terafiliasi dengan company ini via pivot atau legacy
        $isMember = $company->allMembers()->contains('id', $userId) || Auth::user()->isSuperAdmin();
        if (! $isMember) {
            $company->users()->syncWithoutDetaching([$userId => ['role' => Auth::user()->role ?? 'user']]);
        }

        // 1. Ambil semua proyek aktif di company ini
        $companyProjects = Project::where('company_profile_id', $company->id)
            ->where('is_archived', false)
            ->with(['roadmaps', 'tasks'])
            ->latest()
            ->get();

        $projectIds = $companyProjects->pluck('id')->toArray();

        // 2. Tugas Personal User di Company Ini
        $myCompanyTasks = ProjectTask::whereIn('project_id', $projectIds)
            ->where('assigned_to', $userId)
            ->with(['project', 'roadmap'])
            ->latest()
            ->take(6)
            ->get();

        // 3. Tugas Terbuka (Open Pool) di Company Ini yang bisa diklaim
        $openCompanyTasks = ProjectTask::whereIn('project_id', $projectIds)
            ->whereNull('assigned_to')
            ->where('status', '!=', 'Completed')
            ->with('project')
            ->take(4)
            ->get();

        // 4. Agenda & Rapat Mendatang di Company Ini
        $upcomingAgendas = ProjectAgenda::whereIn('project_id', $projectIds)
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();

        // 5. Dokumen Repositori & SOP Company
        $companyDocuments = ProjectDocument::whereIn('project_id', $projectIds)
            ->latest()
            ->take(6)
            ->get();

        // 6. Ringkasan Belanja Transparan (Hanya Proyek yang is_financial_transparent = true atau jika user adalah finance)
        if (Auth::user()->role === 'finance') {
            $transparentProjects = $companyProjects;
        } else {
            $transparentProjects = $companyProjects->where('is_financial_transparent', true);
        }
        $totalCompanyExpenses = ProjectExpense::whereIn('project_id', $transparentProjects->pluck('id'))->sum('amount');

        return view('user.company.workspace', compact(
            'company',
            'companyProjects',
            'myCompanyTasks',
            'openCompanyTasks',
            'upcomingAgendas',
            'companyDocuments',
            'transparentProjects',
            'totalCompanyExpenses'
        ));
    }
}
