<?php

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->manager = User::where('role', 'management')->first();
    $this->employee = User::where('role', 'user')->first();

    $this->project = Project::create([
        'name' => 'Proyek LinkedIn Share',
        'category' => 'Marketing',
        'status' => 'Active',
        'created_by' => $this->manager->id,
    ]);

    $this->task = ProjectTask::create([
        'project_id' => $this->project->id,
        'created_by' => $this->manager->id,
        'assigned_to' => $this->employee->id,
        'title' => 'Menyusun Strategi Sosmed',
        'description' => 'Strategi sosmed untuk Yoimo Workspace',
        'priority' => 'High',
        'status' => 'Completed',
    ]);
});

test('guest can access public task share page with complete open graph meta tags', function () {
    $response = $this->get(route('public.task.show', $this->task->id));

    $response->assertStatus(200);
    $response->assertSee('Menyusun Strategi Sosmed');
    $response->assertSee('Proyek LinkedIn Share');
    $response->assertSee('og:title', false);
    $response->assertSee('og:image', false);
    $response->assertSee('og:description', false);
    $response->assertSee('images/yoimo-achievement-og.png', false);
    $response->assertSee('Bagikan ke LinkedIn', false);
});

test('login page contains full open graph meta tags and banner image', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('og:title', false);
    $response->assertSee('og:description', false);
    $response->assertSee('og:image', false);
    $response->assertSee('images/yoimo-og-banner.png', false);
});
