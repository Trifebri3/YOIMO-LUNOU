<?php

use App\Models\CompanyProfile;
use App\Models\Project;
use App\Models\ProjectMessage;
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

test('demo session censors chat messages and protects credentials features', function () {
    // 1. Log in to start demo session
    $this->post('/demo-login', [
        'token' => 'demo_management',
        'name' => 'Security Test User',
        'email' => 'security@example.com',
        'organization' => 'Security Org',
    ]);

    $trackId = session('demo_track_id');
    expect($trackId)->not->toBeNull();

    // 2. Test chat message censorship accessor
    $message = ProjectMessage::create([
        'sender_id' => 2,
        'message' => 'This is a secret API key or password',
        'attachment_name' => 'sensitive_file.pdf',
        'attachment_file' => 'chat_attachments/secret.pdf',
    ]);

    // Read values and expect them to be censored
    expect($message->message)->toBe('🔒 [Disensor untuk Akun Demo]');
    expect($message->attachment_name)->toBe('🔒 file_disensor.pdf');
    expect($message->attachment_file)->toBeNull();

    $user = User::where('role', 'management')->first();
    $user->email_verified_at = now();
    $user->save();

    // 3. Test AI settings view is empty / protected
    $settingsView = $this->actingAs($user)->withSession(['demo_track_id' => $trackId])->get(route('ai-settings.index'));
    $settingsView->assertStatus(200);
    // Should pass empty settings collection
    $settingsView->assertViewHas('settings', function ($settings) {
        return $settings->isEmpty();
    });

    // 4. Test AI settings modification is blocked
    $this->actingAs($user)->withSession(['demo_track_id' => $trackId])->post(route('ai-settings.store'), [
        'provider' => 'openai',
        'name' => 'Hack OpenAI',
        'api_key' => 'sk-hack123',
        'model' => 'gpt-4o',
    ])->assertSessionHas('error');

    $this->actingAs($user)->withSession(['demo_track_id' => $trackId])->post(route('ai-settings.test'), [
        'provider' => 'openai',
        'model' => 'gpt-4',
        'api_key' => 'test-key',
    ])->assertStatus(403);

    // 5. Test SMTP/WhatsApp credential changes are blocked (as superadmin)
    $superadmin = User::where('role', 'superadmin')->first();
    $superadmin->email_verified_at = now();
    $superadmin->save();

    $this->actingAs($superadmin)->withSession(['demo_track_id' => $trackId])->post(route('superadmin.notification-settings.update'), [
        'mail_host' => 'smtp.hacker.com',
        'mail_port' => '587',
        'mail_username' => 'hacker',
        'mail_encryption' => 'tls',
        'mail_from_address' => 'hacker@mail.com',
        'mail_from_name' => 'Hacker',
        'fonnte_token' => 'hack-token',
        'fonnte_default_target' => '08123456789',
    ])->assertSessionHas('error');

    $this->actingAs($superadmin)->withSession(['demo_track_id' => $trackId])->post(route('superadmin.notification-settings.test-email'), [
        'test_email_address' => 'hacker@mail.com',
    ])->assertSessionHas('error');

    // 6. Test Chat Room contact list is filtered & blocked
    $chatView = $this->actingAs($user)->withSession(['demo_track_id' => $trackId])->get(route('chat.index'));
    $chatView->assertStatus(200);
    $chatView->assertViewHas('contacts', function ($contacts) {
        // Should only contain the demo user (role 'user' Client User or 'management' Manager Eksekutif)
        // And should NOT contain other real users from DatabaseSeeder (like finance, superadmin, etc.)
        foreach ($contacts as $contact) {
            if ($contact->email !== 'management@gmail.com' && $contact->email !== 'user@gmail.com') {
                return false;
            }
        }

        return true;
    });

    $this->actingAs($user)->withSession(['demo_track_id' => $trackId])->post(route('chat.send'), [
        'message' => 'Hack Chat',
    ])->assertStatus(403);
});
