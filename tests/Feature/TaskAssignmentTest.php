<?php

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->manager = User::where('role', 'management')->first();
    $this->employee = User::where('role', 'user')->first();

    // Create self-contained test project
    $this->project = Project::create([
        'name' => 'Test Project',
        'category' => 'Software Development',
        'status' => 'Active',
        'created_by' => $this->manager->id,
        'team_matrix' => [
            ['user_id' => $this->manager->id, 'role' => 'Manager'],
            ['user_id' => $this->employee->id, 'role' => 'Developer'],
        ],
    ]);

    $this->task = ProjectTask::create([
        'project_id' => $this->project->id,
        'created_by' => $this->manager->id,
        'title' => 'Test Assignment Task',
        'description' => 'Verify this task can be directly assigned',
        'priority' => 'High',
        'status' => 'Todo',
    ]);
});

test('manager can directly assign task to a team member', function () {
    // 1. Initially unassigned
    expect($this->task->assigned_to)->toBeNull();

    // 2. Perform direct assignment via management route
    $response = $this->actingAs($this->manager)->post(route('management.projects.tasks.assign', [$this->project->id, $this->task->id]), [
        'assigned_to' => $this->employee->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 3. Verify assignee is updated in database
    $this->task->refresh();
    expect($this->task->assigned_to)->toBe($this->employee->id);

    // 4. Verify assignee received a notification in database
    $notificationsCount = $this->employee->notifications()->count();
    expect($notificationsCount)->toBeGreaterThan(0);
});
