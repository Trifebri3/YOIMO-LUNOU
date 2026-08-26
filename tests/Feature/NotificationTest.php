<?php

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Notifications\TaskNotification;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

    $this->manager = User::where('role', 'management')->first();
    $this->employee = User::where('role', 'user')->first();

    $this->company = CompanyProfile::create([
        'manager_id' => $this->manager->id,
        'company_name' => 'Notification Test Co',
        'slug' => 'notification-test-co',
        'is_published' => true,
    ]);

    $this->project = Project::create([
        'company_profile_id' => $this->company->id,
        'created_by' => $this->manager->id,
        'name' => 'Notification Project',
        'slug' => 'notification-project',
        'status' => 'Active',
    ]);
});

test('task creation notifies assigned user', function () {
    $task = ProjectTask::create([
        'project_id' => $this->project->id,
        'created_by' => $this->manager->id,
        'assigned_to' => $this->employee->id,
        'title' => 'Test Notification Task',
        'priority' => 'High',
        'status' => 'Todo',
    ]);

    // Manually trigger or test through controller
    $response = $this->actingAs($this->manager)->post(route('management.projects.tasks.store', $this->project->id), [
        'title' => 'New Realtime Task',
        'priority' => 'Medium',
        'assigned_to' => $this->employee->id,
    ]);

    $response->assertRedirect();

    // Verify employee received a database notification
    $unreadCount = $this->employee->unreadNotifications()->count();
    expect($unreadCount)->toBeGreaterThan(0);

    $notif = $this->employee->unreadNotifications()->first();
    expect($notif->data['type'])->toBe('task');
    expect($notif->data['title'])->toBe('Tugas Baru Ditugaskan');
});

test('agenda creation notifies attendee users', function () {
    $response = $this->actingAs($this->manager)->post(route('management.projects.agendas.store', $this->project->id), [
        'title' => 'Realtime Meeting Agenda',
        'category' => 'Meeting Online',
        'start_date' => now()->addDay()->toDateString(),
        'recurrence' => 'once',
        'location_type' => 'online',
        'attendee_ids' => [$this->employee->id],
    ]);

    $response->assertRedirect();

    $unreadCount = $this->employee->unreadNotifications()->count();
    expect($unreadCount)->toBeGreaterThan(0);

    $notif = $this->employee->unreadNotifications()->first();
    expect($notif->data['type'])->toBe('agenda');
    expect($notif->data['title'])->toBe('Agenda Kegiatan Baru');
});

test('unread notifications JSON endpoint is accessible', function () {
    // Generate a test database notification
    $task = ProjectTask::create([
        'project_id' => $this->project->id,
        'created_by' => $this->manager->id,
        'assigned_to' => $this->employee->id,
        'title' => 'Check Endpoint Task',
        'priority' => 'High',
        'status' => 'Todo',
    ]);

    $this->employee->notify(new TaskNotification($task, 'created'));

    $response = $this->actingAs($this->employee)->get(route('notifications.unread'));
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'notifications' => [
            '*' => [
                'id',
                'type',
                'title',
                'message',
                'created_at',
            ],
        ],
        'unread_count',
    ]);
});

test('marking notifications as read works', function () {
    $task = ProjectTask::create([
        'project_id' => $this->project->id,
        'created_by' => $this->manager->id,
        'assigned_to' => $this->employee->id,
        'title' => 'Mark Read Task',
        'priority' => 'High',
        'status' => 'Todo',
    ]);

    $this->employee->notify(new TaskNotification($task, 'created'));

    $notif = $this->employee->unreadNotifications()->first();
    expect($notif)->not->toBeNull();

    $response = $this->actingAs($this->employee)->post(route('notifications.mark-read', $notif->id));
    $response->assertStatus(200);

    expect($this->employee->unreadNotifications()->count())->toBe(0);
});

test('marking all notifications as read works', function () {
    $task = ProjectTask::create([
        'project_id' => $this->project->id,
        'created_by' => $this->manager->id,
        'assigned_to' => $this->employee->id,
        'title' => 'Mark All Read Task',
        'priority' => 'High',
        'status' => 'Todo',
    ]);

    $this->employee->notify(new TaskNotification($task, 'created'));
    $this->employee->notify(new TaskNotification($task, 'updated'));

    expect($this->employee->unreadNotifications()->count())->toBe(2);

    $response = $this->actingAs($this->employee)->post(route('notifications.read-all'));
    $response->assertStatus(200);

    expect($this->employee->unreadNotifications()->count())->toBe(0);
});
