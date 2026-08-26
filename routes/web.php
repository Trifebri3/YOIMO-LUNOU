<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboard;
use App\Http\Controllers\Management\ActivityLogController as ManagementActivityLogController;
use App\Http\Controllers\Management\AgendaController as ManagementAgendaController;
use App\Http\Controllers\Management\CompanyProfileController as ManagementCompanyController;
use App\Http\Controllers\Management\DashboardController as ManagementDashboard;
use App\Http\Controllers\Management\DocumentController as ManagementDocumentController;
use App\Http\Controllers\Management\ExpenseController as ManagementExpenseController;
use App\Http\Controllers\Management\LocalAIController;
use App\Http\Controllers\Management\ProjectController as ManagementProjectController;
use App\Http\Controllers\Management\RoadmapController as ManagementRoadmapController;
use App\Http\Controllers\Management\TaskController as ManagementTaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAwardController;
use App\Http\Controllers\PublicCompanyController;
use App\Http\Controllers\Superadmin\AISettingsController;
use App\Http\Controllers\Superadmin\CompanyProfileController as SuperadminCompanyController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboard;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\User\CompanyController as UserCompanyController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\ProjectController as UserProjectController;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DemoDummySeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('/demo-login', function (Request $request) {
    $token = $request->input('token');
    $name = trim($request->input('name'));
    $email = trim($request->input('email'));
    $org = trim($request->input('organization'));

    if (empty($name)) {
        return redirect()->back()->with('error', 'Nama lengkap wajib diisi untuk mencoba demo.');
    }

    if (empty($email)) {
        return redirect()->back()->with('error', 'Alamat email wajib diisi untuk mencoba demo.');
    }

    if ($token !== 'demo_management' && $token !== 'demo_employee') {
        return redirect()->back()->with('error', 'Token demo tidak valid.');
    }

    // Clean up any existing demo tracks for the same email or current session to prevent data accumulation/duplication
    try {
        $trackIdsToDelete = collect();

        if (session()->has('demo_track_id')) {
            $trackIdsToDelete->push(session()->get('demo_track_id'));
        }

        $emailTracks = DB::table('demo_tracks')->where('email', $email)->pluck('id');
        $trackIdsToDelete = $trackIdsToDelete->concat($emailTracks)->unique()->filter();

        if ($trackIdsToDelete->isNotEmpty()) {
            DB::table('demo_tracks')->whereIn('id', $trackIdsToDelete)->delete();
        }
    } catch (Exception $e) {
        Log::error('Failed to clean up old demo tracks: '.$e->getMessage());
    }

    $trackId = null;
    try {
        $trackId = DB::table('demo_tracks')->insertGetId([
            'name' => $name,
            'email' => $email,
            'organization' => $org,
            'token' => $token,
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    } catch (Exception $e) {
        Log::error('Demo tracking failed: '.$e->getMessage());
    }

    $role = ($token === 'demo_management') ? 'management' : 'user';
    session([
        'demo_user_role' => $role,
        'demo_user_name' => $name.' ('.($org ?: 'Personal').')',
        'demo_user_email' => $email,
        'demo_track_id' => $trackId,
    ]);

    if ($trackId) {
        try {
            $seeder = new DemoDummySeeder;
            $seeder->run($trackId);
        } catch (Exception $e) {
            Log::error('Demo dummy seeding failed: '.$e->getMessage());
        }
    }

    $realUser = User::where('role', $role)->first();
    if ($realUser) {
        Auth::login($realUser);
    }

    $targetRoute = ($role === 'management') ? 'management.dashboard' : 'user.dashboard';

    return redirect()->route($targetRoute);
})->name('demo-login-submit');

Route::get('/demo-login', function (Request $request) {
    $token = $request->query('token');
    if ($token === 'demo_management' || $token === 'demo_employee') {
        return redirect()->route('login', ['demo_token' => $token]);
    }

    return redirect()->route('login')->with('error', 'Token demo tidak valid.');
})->name('demo-login');

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        $targetRoute = match ($role) {
            'superadmin' => 'superadmin.dashboard',
            'management' => 'management.dashboard',
            'finance' => 'user.dashboard',
            default => 'user.dashboard',
        };

        return redirect()->route($targetRoute);
    }

    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $role = Auth::user()->role;
    $targetRoute = match ($role) {
        'superadmin' => 'superadmin.dashboard',
        'management' => 'management.dashboard',
        'finance' => 'user.dashboard',
        default => 'user.dashboard',
    };

    return redirect()->route($targetRoute);
})->name('dashboard');

// Portofolio Publik Perusahaan
Route::get('/company/{slug}', [PublicCompanyController::class, 'show'])->name('public.company.show');

// Public Award Certificate Share Link
Route::get('/award/share/{token}', [PublicAwardController::class, 'show'])->name('public.award.show');

// Client Shared Portal Links
Route::get('/shared/project/{token}', [ClientPortalController::class, 'show'])->name('client.portal.show');
Route::post('/shared/project/{token}/ask', [ClientPortalController::class, 'submitQuestion'])->name('client.portal.ask');

// 1. Superadmin Area
Route::middleware(['auth', 'verified', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperadminDashboard::class, 'index'])->name('dashboard');
        Route::get('/notification-settings', [SuperadminDashboard::class, 'notificationSettings'])->name('notification-settings.index');
        Route::post('/notification-settings', [SuperadminDashboard::class, 'updateNotificationSettings'])->name('notification-settings.update');
        Route::post('/notification-settings/test-email', [SuperadminDashboard::class, 'testEmail'])->name('notification-settings.test-email');
        Route::post('/notification-settings/test-whatsapp', [SuperadminDashboard::class, 'testWhatsapp'])->name('notification-settings.test-whatsapp');
        Route::resource('users', UserController::class);
        Route::resource('company', SuperadminCompanyController::class);
    });

// AI Settings (Scoped to each user account)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/ai-settings', [AISettingsController::class, 'index'])->name('ai-settings.index');
    Route::post('/ai-settings', [AISettingsController::class, 'store'])->name('ai-settings.store');
    Route::put('/ai-settings/{aiSetting}', [AISettingsController::class, 'update'])->name('ai-settings.update');
    Route::delete('/ai-settings/{aiSetting}', [AISettingsController::class, 'destroy'])->name('ai-settings.destroy');
    Route::post('/ai-settings/{aiSetting}/activate', [AISettingsController::class, 'activate'])->name('ai-settings.activate');
    Route::post('/ai-settings/test', [AISettingsController::class, 'test'])->name('ai-settings.test');
});

// 2. Management Area (Workspace, Project, Linimasa, Tasks, & Portfolio Settings)
Route::middleware(['auth', 'verified', 'role:management,superadmin'])
    ->prefix('management')
    ->name('management.')
    ->group(function () {
        Route::get('/dashboard', [ManagementDashboard::class, 'index'])->name('dashboard');
        Route::get('/company/{company}/workspace', [ManagementCompanyController::class, 'workspace'])->name('company.workspace');
        Route::post('/company/{company}/add-user', [ManagementCompanyController::class, 'addUser'])->name('company.add-user');
        Route::get('/company/{company}/wellbeing', [ManagementCompanyController::class, 'wellbeingReport'])->name('company.wellbeing');
        Route::resource('company', ManagementCompanyController::class)->except(['create', 'store', 'destroy']);
        Route::post('/company/{company}/generate-ai', [ManagementCompanyController::class, 'generateCompanyAi'])->name('company.generate-ai');

        // AI Generator
        Route::post('/projects/generate-ai', [ManagementProjectController::class, 'generateAi'])->name('projects.generate-ai');

        // Quick Assign Team & Portfolio Settings
        Route::post('/projects/{project}/assign-team', [ManagementProjectController::class, 'assignTeam'])->name('projects.assign-team');
        Route::get('/projects/{project}/portfolio', [ManagementProjectController::class, 'editPortfolio'])->name('projects.portfolio.edit');
        Route::put('/projects/{project}/portfolio', [ManagementProjectController::class, 'updatePortfolio'])->name('projects.portfolio.update');
        Route::post('/projects/{project}/portfolio/generate-ai', [ManagementProjectController::class, 'generatePortfolioAi'])->name('projects.portfolio.generate-ai');

        // Sub-Modul Linimasa / Roadmap
        Route::post('/projects/{project}/roadmaps/generate-ai', [ManagementRoadmapController::class, 'generateRoadmapAi'])->name('projects.roadmaps.generate-ai');
        Route::post('/projects/{project}/roadmaps/suggest-objectives-ai', [ManagementRoadmapController::class, 'suggestObjectivesAi'])->name('projects.roadmaps.suggest-objectives-ai');
        Route::post('/projects/{project}/roadmaps/{roadmap}/enhance-ai', [ManagementRoadmapController::class, 'enhanceRoadmapAi'])->name('projects.roadmaps.enhance-ai');
        Route::resource('projects.roadmaps', ManagementRoadmapController::class)->except(['create', 'show', 'edit']);

        // Di dalam Route::prefix('management')->group(...)
        Route::patch('/projects/{project}/agendas/{agenda}/status', [ManagementAgendaController::class, 'updateStatus'])->name('projects.agendas.update-status');
        Route::post('/projects/{project}/agendas/generate-ai', [ManagementAgendaController::class, 'generateAgendaAi'])->name('projects.agendas.generate-ai');
        Route::post('/projects/{project}/agendas/generate-bulk-ai', [ManagementAgendaController::class, 'generateBulkAgendasAi'])->name('projects.agendas.generate-bulk-ai');
        Route::resource('projects.agendas', ManagementAgendaController::class)->except(['create', 'show', 'edit', 'update']);

        Route::post('/projects/{project}/tasks/generate-ai', [ManagementProjectController::class, 'generateTaskAi'])->name('projects.tasks.generate-ai');
        Route::post('/projects/{project}/tasks/generate-roadmap-tasks-ai', [ManagementTaskController::class, 'generateTasksAi'])->name('projects.tasks.generate-roadmap-tasks-ai');
        Route::post('/projects/{project}/tasks/add-single-ai', [ManagementTaskController::class, 'addSingleTaskAi'])->name('projects.tasks.add-single-ai');
        Route::patch('/projects/{project}/tasks/{task}/status', [ManagementTaskController::class, 'updateStatus'])->name('projects.tasks.update-status');
        Route::post('/projects/{project}/tasks/{task}/submit-report', [ManagementTaskController::class, 'submitReport'])->name('projects.tasks.submit-report');
        Route::resource('projects.tasks', ManagementTaskController::class)->except(['create', 'show', 'edit', 'update']);

        // Tasks Import/Export CSV
        Route::get('/projects/{project}/tasks-export', [ManagementTaskController::class, 'exportCsv'])->name('projects.tasks.export');
        Route::post('/projects/{project}/tasks-import', [ManagementTaskController::class, 'importCsv'])->name('projects.tasks.import');
        Route::get('/projects/{project}/tasks-template', [ManagementTaskController::class, 'downloadTemplate'])->name('projects.tasks.template');

        // Projects Import/Export CSV
        Route::get('/projects-export', [ManagementProjectController::class, 'exportCsv'])->name('projects.export');
        Route::post('/projects-import', [ManagementProjectController::class, 'importCsv'])->name('projects.import');
        Route::get('/projects-template', [ManagementProjectController::class, 'downloadTemplate'])->name('projects.template');

        // Client Portal Management
        Route::post('/projects/{project}/share-token', [ManagementProjectController::class, 'generateShareToken'])->name('projects.share-token');
        Route::post('/projects/{project}/disable-share', [ManagementProjectController::class, 'disableShareToken'])->name('projects.disable-share');
        Route::post('/projects/questions/{question}/answer', [ManagementProjectController::class, 'answerQuestion'])->name('projects.questions.answer');

        // Main Project Resource
        Route::resource('projects', ManagementProjectController::class);
        Route::post('/ai-generate', [LocalAIController::class, 'generate'])->name('ai-generate');
        // Rute Klaim Tugas Terbuka (Claim Task)
        Route::post('/projects/{project}/tasks/{task}/claim', [ManagementTaskController::class, 'claim'])->name('projects.tasks.claim');

        // Di dalam Route::prefix('management')->group(...)
        Route::patch('/projects/{project}/expenses/toggle-transparency', [ManagementExpenseController::class, 'toggleTransparency'])->name('projects.expenses.toggle-transparency');
        Route::resource('projects.expenses', ManagementExpenseController::class)->except(['create', 'show', 'edit', 'update']);
        Route::resource('projects.documents', ManagementDocumentController::class)->except(['create', 'edit', 'update']);
        Route::get('/projects/{project}/logs', [ManagementActivityLogController::class, 'index'])->name('projects.logs.index');
        Route::post('/projects/{project}/tasks/{task}/review', [ManagementTaskController::class, 'reviewTask'])->name('projects.tasks.review');
    });

// 3. Finance Area
Route::middleware(['auth', 'verified', 'role:finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/dashboard', [FinanceDashboard::class, 'index'])->name('dashboard');
    });

// Profile Routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Live WhatsApp Chat Center
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');

    // Floating Mascot AI Chatbot Companion
    Route::post('/chatbot/query', [ChatbotController::class, 'query'])->name('chatbot.query');
});

// Import statements moved to the top of the file

// 4. Regular User Area
Route::middleware(['auth', 'verified', 'role:user,finance,management,superadmin'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
        Route::get('/company/{company}/workspace', [UserCompanyController::class, 'workspace'])->name('company.workspace');

        // Workspace Proyek & Aksi Tugas Lengkap
        Route::get('/projects/{project}', [UserProjectController::class, 'show'])->name('projects.show');
        Route::post('/projects/{project}/discuss-ai', [UserProjectController::class, 'discussProjectAI'])->name('projects.discuss-ai');
        Route::post('/projects/{project}/tasks/{task}/claim', [UserProjectController::class, 'claimTask'])->name('projects.tasks.claim');
        Route::post('/projects/{project}/tasks/{task}/start', [UserProjectController::class, 'startTask'])->name('projects.tasks.start');
        Route::post('/projects/{project}/tasks/{task}/partial-log', [UserProjectController::class, 'logPartialProgress'])->name('projects.tasks.partial-log');
        Route::post('/projects/{project}/tasks/{task}/submit-final', [UserProjectController::class, 'submitFinal'])->name('projects.tasks.submit-final');
        Route::post('/tasks/auto-fill', [UserProjectController::class, 'autoFillTask'])->name('tasks.auto-fill');

        // Expenses CRUD for finance role
        Route::post('/projects/{project}/expenses', [UserProjectController::class, 'storeExpense'])->name('projects.expenses.store');
        Route::delete('/projects/{project}/expenses/{expense}', [UserProjectController::class, 'destroyExpense'])->name('projects.expenses.destroy');

        // Wellbeing Workspace Routes
        Route::post('/wellbeing/checkin', [UserDashboard::class, 'checkin'])->name('wellbeing.checkin');
        Route::post('/wellbeing/journal', [UserDashboard::class, 'storeJournal'])->name('wellbeing.journal');
        Route::post('/wellbeing/goal', [UserDashboard::class, 'storeGoal'])->name('wellbeing.goal');
        Route::post('/wellbeing/goal/{goal}/toggle', [UserDashboard::class, 'toggleGoal'])->name('wellbeing.toggle-goal');
        Route::delete('/wellbeing/goal/{goal}', [UserDashboard::class, 'destroyGoal'])->name('wellbeing.destroy-goal');
        Route::post('/wellbeing/left-thought', [UserDashboard::class, 'leftThought'])->name('wellbeing.left-thought');
        Route::post('/wellbeing/reflection', [UserDashboard::class, 'storeReflection'])->name('wellbeing.reflection');
        Route::get('/wellbeing/balance-report', [UserDashboard::class, 'balanceReport'])->name('wellbeing.balance-report');
        Route::post('/wellbeing/discuss', [UserDashboard::class, 'discuss'])->name('wellbeing.discuss');
        Route::post('/wellbeing/checkin-auto-fill', [UserDashboard::class, 'autoFillCheckin'])->name('wellbeing.checkin-auto-fill');
        Route::post('/wellbeing/auto-journal', [UserDashboard::class, 'autoJournal'])->name('wellbeing.auto-journal');
        Route::post('/wellbeing/award-points', [UserDashboard::class, 'awardPoints'])->name('wellbeing.award-points');
        Route::post('/wellbeing/generate-logic-quiz', [UserDashboard::class, 'generateLogicQuiz'])->name('wellbeing.generate-logic-quiz');
    });

require __DIR__.'/auth.php';
