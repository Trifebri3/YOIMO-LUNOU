<?php

use App\Models\CompanyProfile;
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

    // Create another user not associated with company
    $otherUser = User::create([
        'name' => 'Stranger',
        'email' => 'stranger@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
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

    // Create another user not associated with company
    $otherUser = User::create([
        'name' => 'Stranger In Project',
        'email' => 'strangerproj@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
    ]);

    $response = $this->actingAs($manager)
        ->get(route('management.projects.create', ['company_id' => $this->company->id]));

    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertDontSee($otherUser->name);
});

test('management can add a new user to their company workspace', function () {
    $manager = User::where('role', 'management')->first();

    $response = $this->actingAs($manager)
        ->post(route('management.company.add-user', $this->company->id), [
            'name' => 'New Workspace Employee',
            'email' => 'newemployee@example.com',
            'phone' => '08122334455',
            'position' => 'UI Designer',
            'role' => 'user',
            'password' => 'password123',
        ]);

    $response->assertRedirect();

    $newEmployee = User::where('email', 'newemployee@example.com')->first();
    expect($newEmployee)->not->toBeNull();
    expect($newEmployee->company_profile_id)->toBe($this->company->id);
    expect($newEmployee->phone)->toBe('628122334455'); // Standardized phone number mutator check!
});
