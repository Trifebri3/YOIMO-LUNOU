<?php

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    // Seed the default users needed by the seeder (manager & employee)
    $this->seed(DatabaseSeeder::class);
});

test('demo login seeds isolated company DUMY and project', function () {
    $response = $this->post('/demo-login', [
        'token' => 'demo_management',
        'name' => 'Test Demo User',
        'email' => 'testdemo@example.com',
        'organization' => 'Test Org',
    ]);

    // Check redirection to management dashboard
    $response->assertRedirect(route('management.dashboard'));

    // Check user is authenticated as management
    $this->assertAuthenticated();
    expect(Auth::user()->role)->toBe('management');

    // Check track was created
    $track = DB::table('demo_tracks')->where('email', 'testdemo@example.com')->first();
    expect($track)->not->toBeNull();

    // Check that DUMY company profile was seeded for this track
    // We temporarily disable global scope to verify it exists in DB
    $company = CompanyProfile::withoutGlobalScopes()->where('demo_track_id', $track->id)->first();
    expect($company)->not->toBeNull();
    expect($company->company_name)->toBe('DUMY');

    // Check that Project was seeded for this company & track
    $project = Project::withoutGlobalScopes()->where('demo_track_id', $track->id)->first();
    expect($project)->not->toBeNull();
    expect($project->company_profile_id)->toBe($company->id);
    expect($project->name)->toBe('Pengembangan Sistem ERP Terintegrasi');

    // Check that tasks and roadmaps were seeded too
    expect($project->roadmaps()->withoutGlobalScopes()->count())->toBe(2);
    expect($project->tasks()->withoutGlobalScopes()->count())->toBe(4);
});

test('demo global scope isolates companies and projects between sessions', function () {
    // 1. Create first demo session
    $this->post('/demo-login', [
        'token' => 'demo_management',
        'name' => 'User A',
        'email' => 'usera@example.com',
        'organization' => 'Org A',
    ]);

    $trackAId = session('demo_track_id');
    expect($trackAId)->not->toBeNull();

    // Get count of visible companies for Session A
    expect(CompanyProfile::count())->toBe(1);
    expect(Project::count())->toBe(1);

    // Logout Session A
    Auth::logout();
    Session::flush();

    // 2. Create second demo session
    $this->post('/demo-login', [
        'token' => 'demo_management',
        'name' => 'User B',
        'email' => 'userb@example.com',
        'organization' => 'Org B',
    ]);

    $trackBId = session('demo_track_id');
    expect($trackBId)->not->toBeNull();
    expect($trackBId)->not->toBe($trackAId);

    // Session B should only see its own 1 company and project (not User A's)
    expect(CompanyProfile::count())->toBe(1);
    expect(Project::count())->toBe(1);

    // Verify database actually contains both (without global scopes)
    expect(CompanyProfile::withoutGlobalScopes()->count())->toBe(2);
    expect(Project::withoutGlobalScopes()->count())->toBe(2);
});

test('demo cleanup middleware deletes expired tracks and cascades deletes to workspaces', function () {
    // 1. Log in to create a track and workspace
    $this->post('/demo-login', [
        'token' => 'demo_management',
        'name' => 'Expired User',
        'email' => 'expired@example.com',
        'organization' => 'Expired Org',
    ]);

    $trackId = session('demo_track_id');
    expect($trackId)->not->toBeNull();

    // Confirm DB has the entities
    expect(DB::table('demo_tracks')->where('id', $trackId)->exists())->toBeTrue();
    expect(CompanyProfile::withoutGlobalScopes()->where('demo_track_id', $trackId)->exists())->toBeTrue();
    expect(Project::withoutGlobalScopes()->where('demo_track_id', $trackId)->exists())->toBeTrue();

    // 2. Manually modify the created_at of the track to 4 hours ago to simulate expiration
    DB::table('demo_tracks')->where('id', $trackId)->update([
        'created_at' => Carbon::now()->subHours(4),
    ]);

    // Clear cleanup cooldown cache to force cleanup to run on next request
    Cache::forget('demo_cleanup_cooldown');

    // 3. Make a request to trigger the DemoAuthMiddleware
    $this->get('/');

    // 4. Verify track and all cascade-related entries are completely deleted
    expect(DB::table('demo_tracks')->where('id', $trackId)->exists())->toBeFalse();
    expect(CompanyProfile::withoutGlobalScopes()->where('demo_track_id', $trackId)->exists())->toBeFalse();
    expect(Project::withoutGlobalScopes()->where('demo_track_id', $trackId)->exists())->toBeFalse();

    // Verify nested items (e.g. roadmaps and tasks) are also gone due to cascade delete
    expect(DB::table('project_roadmaps')->count())->toBe(0);
    expect(DB::table('project_tasks')->count())->toBe(0);
});
