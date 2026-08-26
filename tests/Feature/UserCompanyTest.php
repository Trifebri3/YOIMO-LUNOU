<?php

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

    // Create a dummy company for testing
    $manager = User::where('role', 'management')->first();
    $this->company = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'Test Company',
        'slug' => 'test-company',
        'is_published' => true,
    ]);
});

test('superadmin can assign a user to a company profile', function () {
    $superadmin = User::where('role', 'superadmin')->first();
    $user = User::where('role', 'user')->first();

    $response = $this->actingAs($superadmin)
        ->put(route('superadmin.users.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '62812345678',
            'position' => 'Developer',
            'role' => 'user',
            'company_profile_id' => $this->company->id,
        ]);

    $response->assertRedirect(route('superadmin.users.index'));

    $user->refresh();
    expect($user->company_profile_id)->toBe($this->company->id);
});

test('workspace dashboard lists only embedded company employees', function () {
    $manager = User::where('role', 'management')->first();

    // Associate user with company
    $user = User::where('role', 'user')->first();
    $user->update(['company_profile_id' => $this->company->id]);

    // Create another company for isolation testing
    $otherCompany = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'Other Company A',
        'slug' => 'other-company-a',
        'is_published' => true,
    ]);

    // Create another user associated with other company
    $otherUser = User::create([
        'name' => 'Stranger',
        'email' => 'stranger@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
        'company_profile_id' => $otherCompany->id,
    ]);

    $response = $this->actingAs($manager)
        ->get(route('management.company.workspace', $this->company->id));

    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertDontSee($otherUser->name);
});

test('project create page filters selectable team members by company', function () {
    $manager = User::where('role', 'management')->first();

    // Associate user with company
    $user = User::where('role', 'user')->first();
    $user->update(['company_profile_id' => $this->company->id]);

    // Create another company for isolation testing
    $otherCompany = CompanyProfile::create([
        'manager_id' => $manager->id,
        'company_name' => 'Other Company B',
        'slug' => 'other-company-b',
        'is_published' => true,
    ]);

    // Create another user associated with other company
    $otherUser = User::create([
        'name' => 'Stranger In Project',
        'email' => 'strangerproj@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
        'company_profile_id' => $otherCompany->id,
    ]);

    $response = $this->actingAs($manager)
        ->get(route('management.projects.create', ['company_id' => $this->company->id]));

    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertDontSee($otherUser->name);
});

test('management can add an existing user to their company workspace', function () {
    $manager = User::where('role', 'management')->first();

    // Create an unassigned user
    $unassignedUser = User::create([
        'name' => 'Unassigned Tester',
        'email' => 'unassigned@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
        'phone' => '628122334455',
    ]);

    $response = $this->actingAs($manager)
        ->post(route('management.company.add-user', $this->company->id), [
            'user_id' => $unassignedUser->id,
        ]);

    $response->assertRedirect();

    $unassignedUser->refresh();
    expect($unassignedUser->company_profile_id)->toBe($this->company->id);
});

test('management can archive and restore a project', function () {
    $manager = User::where('role', 'management')->first();

    // Create a project
    $project = Project::create([
        'company_profile_id' => $this->company->id,
        'created_by' => $manager->id,
        'name' => 'Archive Tester Project',
        'slug' => 'archive-tester-project',
        'status' => 'Active',
    ]);

    // Verify it appears in active project list
    $response = $this->actingAs($manager)->get(route('management.projects.index'));
    $response->assertStatus(200);
    $response->assertSee('Archive Tester Project');

    // Archive the project
    $response = $this->actingAs($manager)->post(route('management.projects.toggle-archive', $project->id));
    $response->assertRedirect();

    $project->refresh();
    expect($project->is_archived)->toBeTrue();

    // Verify it is hidden from active project list
    $response = $this->actingAs($manager)->get(route('management.projects.index'));
    $response->assertStatus(200);
    $response->assertDontSee('Archive Tester Project');

    // Verify it appears in archived project list
    $response = $this->actingAs($manager)->get(route('management.projects.index', ['filter' => 'archived']));
    $response->assertStatus(200);
    $response->assertSee('Archive Tester Project');

    // Verify it is hidden from chat group project list
    $response = $this->actingAs($manager)->get(route('chat.index'));
    $response->assertStatus(200);
    $response->assertDontSee('Archive Tester Project');

    // Restore the project
    $response = $this->actingAs($manager)->post(route('management.projects.toggle-archive', $project->id));
    $response->assertRedirect();

    $project->refresh();
    expect($project->is_archived)->toBeFalse();

    // Verify it appears in active list again
    $response = $this->actingAs($manager)->get(route('management.projects.index'));
    $response->assertStatus(200);
    $response->assertSee('Archive Tester Project');
});
