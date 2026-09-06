<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->employee = User::where('role', 'user')->first();
    $this->manager = User::where('role', 'management')->first();

    $this->project = Project::create([
        'name' => 'Proyek Navigasi Cerdas',
        'category' => 'Web App',
        'status' => 'Active',
        'created_by' => $this->manager->id,
        'team_matrix' => [
            ['user_id' => $this->employee->id, 'role' => 'Developer'],
        ],
    ]);

    $this->project2 = Project::create([
        'name' => 'Proyek Kedua Karyawan',
        'category' => 'Mobile App',
        'status' => 'Active',
        'created_by' => $this->manager->id,
        'team_matrix' => [
            ['user_id' => $this->employee->id, 'role' => 'Designer'],
        ],
    ]);
});

test('member portal dashboard renders universal quick nav modal and navigation elements', function () {
    $response = $this->actingAs($this->employee)->get(route('user.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('quickNavModal', false);
    $response->assertSee('Cari proyek, menu, atau tugas', false);
    $response->assertSee('mobile_bottom_nav', false);
    $response->assertSee('Ctrl K', false);
});

test('project show page renders sticky sub-navigation breadcrumbs and back button', function () {
    $response = $this->actingAs($this->employee)->get(route('user.projects.show', $this->project->id));

    $response->assertStatus(200);
    $response->assertSee('Proyek Navigasi Cerdas');
    $response->assertSee('Dashboard', false);
    $response->assertSee('quickProjSwitchDropdown', false);
});

test('management dashboard renders navigation header and quick nav modal', function () {
    $response = $this->actingAs($this->manager)->get(route('management.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('quickNavModal', false);
    $response->assertSee('MANAGEMENT HUB', false);
});

test('logout button is easily accessible in header dropdown, mobile drawer, quick nav, and profile', function () {
    // 1. In Member Dashboard: Check Header Dropdown & Drawer & Quick Nav
    $response = $this->actingAs($this->employee)->get(route('user.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('userHeaderProfileContainer', false);
    $response->assertSee('Keluar (Log Out)', false);
    $response->assertSee('action="'.route('logout').'"', false);

    // 2. In Profile Edit: Check Session & Logout card
    $profileResponse = $this->actingAs($this->employee)->get(route('profile.edit'));
    $profileResponse->assertStatus(200);
    $profileResponse->assertSee('Sesi Login');
    $profileResponse->assertSee('action="'.route('logout').'"', false);

    // 3. User can actually log out directly to login page
    $logoutResponse = $this->actingAs($this->employee)->post(route('logout'));
    $logoutResponse->assertRedirect(route('login'));
    $this->assertGuest();
});

test('pwa manifest, service worker, and lunou icons are valid and properly configured', function () {
    // 1. Manifest verification
    $manifestPath = public_path('manifest.json');
    expect(file_exists($manifestPath))->toBeTrue();

    $manifest = json_decode(file_get_contents($manifestPath), true);
    expect($manifest)->toBeArray();
    expect($manifest['name'])->toBe('Yoimo Workspace');
    expect($manifest['short_name'])->toBe('Yoimo');
    expect($manifest['icons'])->toBeArray();
    expect(count($manifest['icons']))->toBeGreaterThanOrEqual(8);

    // 2. Service Worker file verification (Network-First & bypass logout)
    $swContent = file_get_contents(public_path('sw.js'));
    expect($swContent)->toContain('yoimo-lunou-pwa-v3');
    expect($swContent)->toContain('offline.html');
    expect($swContent)->toContain("'/logout'");
    expect($swContent)->toContain("'/ping'");
    expect($swContent)->not->toContain("  '/',");

    // 3. Offline page verification
    $offlineContent = file_get_contents(public_path('offline.html'));
    expect($offlineContent)->toContain('Koneksi Sedang Terputus');
    expect($offlineContent)->toContain('LUNOU');

    // 4. LUNOU Icon files exist on disk with correct sizes
    expect(file_exists(public_path('icons/icon-192x192.png')))->toBeTrue();
    expect(file_exists(public_path('icons/icon-512x512.png')))->toBeTrue();
    expect(file_exists(public_path('icons/maskable-icon-512x512.png')))->toBeTrue();
    expect(file_exists(public_path('icons/apple-touch-icon.png')))->toBeTrue();
});

test('session heartbeat ping endpoint returns fresh csrf token and auth status', function () {
    // Guest ping
    $response = $this->getJson(route('ping'));
    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'ok',
        'authenticated' => false,
    ]);
    expect($response->json('csrf_token'))->toBeString()->not->toBeEmpty();

    // Authenticated ping
    $authResponse = $this->actingAs($this->employee)->getJson(route('ping'));
    $authResponse->assertStatus(200);
    $authResponse->assertJson([
        'status' => 'ok',
        'authenticated' => true,
        'user' => [
            'id' => $this->employee->id,
            'name' => $this->employee->name,
            'role' => $this->employee->role,
        ],
    ]);
});
