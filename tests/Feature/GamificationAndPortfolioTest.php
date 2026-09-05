<?php

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\UserAward;
use App\Models\UserPoint;
use App\Models\UserPointLog;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can belong to multiple companies simultaneously via pivot table', function () {
    $manager1 = User::factory()->create(['role' => 'management']);
    $manager2 = User::factory()->create(['role' => 'management']);

    $company1 = CompanyProfile::create([
        'manager_id' => $manager1->id,
        'company_name' => 'PT Nusantara Satu',
        'is_published' => true,
    ]);

    $company2 = CompanyProfile::create([
        'manager_id' => $manager2->id,
        'company_name' => 'PT Global Dua',
        'is_published' => true,
    ]);

    $employee = User::factory()->create(['role' => 'user']);

    // Attach to company 1
    $company1->users()->syncWithoutDetaching([$employee->id => ['role' => 'user']]);
    // Attach to company 2
    $company2->users()->syncWithoutDetaching([$employee->id => ['role' => 'user']]);

    $employeeCompanies = $employee->allCompanies();
    expect($employeeCompanies)->toHaveCount(2);
    expect($employeeCompanies->pluck('id'))->toContain($company1->id);
    expect($employeeCompanies->pluck('id'))->toContain($company2->id);

    // Verify company1 and company2 rosters both contain employee
    expect($company1->allMembers()->pluck('id'))->toContain($employee->id);
    expect($company2->allMembers()->pluck('id'))->toContain($employee->id);
});

test('submitting a final task awards instant submission points with tracing log', function () {
    $user = User::factory()->create(['role' => 'user']);
    $manager = User::factory()->create(['role' => 'management']);

    $company = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'PT Gamifikasi Sukses',
        'is_published' => true,
    ]);

    $project = Project::create([
        'company_profile_id' => $company->id,
        'name' => 'Proyek Alpha',
        'category' => 'Technology',
        'status' => 'Active',
        'created_by' => $manager->id,
        'team_matrix' => [
            ['user_id' => (string) $user->id, 'role' => 'Developer'],
        ],
    ]);

    $task = ProjectTask::create([
        'project_id' => $project->id,
        'title' => 'Slicing Halaman Dashboard',
        'priority' => 'High',
        'status' => 'In Progress',
        'assigned_to' => $user->id,
        'created_by' => $manager->id,
        'due_date' => Carbon::tomorrow(),
    ]);

    // Employee submits task final
    $response = $this->actingAs($user)->post(route('user.projects.tasks.submit-final', [$project->id, $task->id]), [
        'submission_notes' => 'Pekerjaan selesai 100% dan sudah diuji di Chrome dan Firefox.',
        'submission_link' => 'https://github.com/example/repo',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    // Verify UserPoint has 25 points
    $userPoint = UserPoint::where('user_id', $user->id)->first();
    expect($userPoint)->not->toBeNull();
    expect($userPoint->total_points)->toBe(25);

    // Verify UserPointLog has entry
    $log = UserPointLog::where('user_id', $user->id)
        ->where('source_type', 'task_submitted')
        ->where('source_id', $task->id)
        ->first();
    expect($log)->not->toBeNull();
    expect($log->points)->toBe(25);
    expect($log->description)->toContain('Slicing Halaman Dashboard');
});

test('task completion awards base points, priority bonus, on-time bonus, and records tracing', function () {
    $user = User::factory()->create(['role' => 'user']);
    $manager = User::factory()->create(['role' => 'management']);

    $company = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'PT Gamifikasi Pro',
        'is_published' => true,
    ]);

    $project = Project::create([
        'company_profile_id' => $company->id,
        'name' => 'Proyek Beta',
        'category' => 'Technology',
        'status' => 'Active',
        'created_by' => $manager->id,
    ]);

    $task = ProjectTask::create([
        'project_id' => $project->id,
        'title' => 'Integrasi Payment Gateway',
        'priority' => 'Urgent',
        'status' => 'Review',
        'assigned_to' => $user->id,
        'created_by' => $manager->id,
        'due_date' => Carbon::tomorrow(),
    ]);

    // Manager approves the task
    $response = $this->actingAs($manager)->post(route('management.projects.tasks.review', [$project->id, $task->id]), [
        'decision' => 'Approve',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    // Urgent task completion: Base 50 + Urgent 30 = 80 XP
    // On-Time bonus: 30 XP
    // Total should be 110 XP
    $userPoint = UserPoint::where('user_id', $user->id)->first();
    expect($userPoint)->not->toBeNull();
    expect($userPoint->total_points)->toBe(110);

    // Verify task_completed tracing log
    $completionLog = UserPointLog::where('user_id', $user->id)
        ->where('source_type', 'task_completed')
        ->where('source_id', $task->id)
        ->first();
    expect($completionLog)->not->toBeNull();
    expect($completionLog->points)->toBe(80);

    // Verify task_ontime tracing log
    $onTimeLog = UserPointLog::where('user_id', $user->id)
        ->where('source_type', 'task_ontime')
        ->where('source_id', $task->id)
        ->first();
    expect($onTimeLog)->not->toBeNull();
    expect($onTimeLog->points)->toBe(30);
});

test('completing 3 on-time tasks unlocks Si Paling Tepat Waktu badge', function () {
    $user = User::factory()->create(['role' => 'user']);
    $manager = User::factory()->create(['role' => 'management']);

    $company = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'PT Milestone',
        'is_published' => true,
    ]);

    $project = Project::create([
        'company_profile_id' => $company->id,
        'name' => 'Proyek Gamifikasi',
        'category' => 'Technology',
        'status' => 'Active',
        'created_by' => $manager->id,
    ]);

    for ($i = 1; $i <= 3; $i++) {
        $task = ProjectTask::create([
            'project_id' => $project->id,
            'title' => "Tugas On-Time {$i}",
            'priority' => 'Medium',
            'status' => 'In Progress',
            'assigned_to' => $user->id,
            'created_by' => $manager->id,
            'due_date' => Carbon::tomorrow(),
        ]);

        GamificationService::awardTaskCompletion($task);
    }

    $award = UserAward::where('user_id', $user->id)
        ->where('award_type', 'si_paling_tepat_waktu')
        ->first();

    expect($award)->not->toBeNull();
    expect($award->title)->toBe('Si Paling Tepat Waktu');
});

test('public portfolio page can be accessed by guests and shows user projects and stats', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'position' => 'Senior Fullstack Engineer',
        'bio' => 'Passionate in building scalable web apps and collaborative systems.',
    ]);

    $manager = User::factory()->create(['role' => 'management']);
    $company = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'PT Inovasi Digital',
        'is_published' => true,
    ]);

    $project = Project::create([
        'company_profile_id' => $company->id,
        'name' => 'Sistem Rekrutmen AI',
        'category' => 'Artificial Intelligence',
        'status' => 'Active',
        'created_by' => $manager->id,
        'team_matrix' => [
            ['user_id' => (string) $user->id, 'role' => 'Tech Lead'],
        ],
    ]);

    // Give points & badge
    GamificationService::addPoints($user->id, 250, 'test_points', null, $company->id, $project->id, 'Poin kontribusi');
    GamificationService::awardBadge($user->id, 'si_paling_produktif', 'Si Paling Produktif');

    // Guest request (unauthenticated)
    $response = $this->get(route('public.portfolio.show', $user->id));

    $response->assertStatus(200);
    $response->assertSee('Budi Santoso');
    $response->assertSee('Senior Fullstack Engineer');
    $response->assertSee('Sistem Rekrutmen AI');
    $response->assertSee('Si Paling Produktif');
    $response->assertSee('Level 2'); // 250 XP is Level 2
    $response->assertSee('Bagikan ke LinkedIn');
    $response->assertSee('WhatsApp');
});
