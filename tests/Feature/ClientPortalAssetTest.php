<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->user = User::where('role', 'management')->first();

    $this->project = Project::create([
        'name' => 'Kopi Kenangan Rebranding Project',
        'category' => 'Web Development',
        'status' => 'In Progress',
        'created_by' => $this->user->id,
        'share_token' => Str::random(32),
    ]);
});

test('guest client sees auto-seeded default asset requirements on first portal visit', function () {
    expect($this->project->assetRequirements()->count())->toBe(0);

    $response = $this->get(route('client.portal.show', $this->project->share_token));
    $response->assertStatus(200);

    // Auto-seed should have generated 4 default requirements
    expect($this->project->assetRequirements()->count())->toBe(4);

    $this->assertDatabaseHas('project_asset_requirements', [
        'project_id' => $this->project->id,
        'title' => 'Logo Perusahaan / Vektor Asli',
        'category' => 'Branding',
        'is_mandatory' => true,
    ]);

    $response->assertSee('Formulir Pengumpulan Aset', false);
    $response->assertSee('Logo Perusahaan / Vektor Asli');
    $response->assertSee('Materi Konten Teks');
});

test('client can submit asset with uploaded file', function () {
    Storage::fake('public');

    // Trigger auto-seed
    $this->get(route('client.portal.show', $this->project->share_token));

    $asset = $this->project->assetRequirements()->where('title', 'Logo Perusahaan / Vektor Asli')->first();
    expect($asset)->not->toBeNull();
    expect($asset->status)->toBe('pending');

    $fakeFile = UploadedFile::fake()->create('logo_perusahaan.png', 500, 'image/png');

    $response = $this->post(
        route('client.portal.assets.submit', [$this->project->share_token, $asset->id]),
        [
            'client_name' => 'Budi Client',
            'file' => $fakeFile,
            'client_notes' => 'Berikut logo transparan resolusi tinggi kami.',
        ],
        ['X-Requested-With' => 'XMLHttpRequest']
    );

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'asset' => [
            'id' => $asset->id,
            'status' => 'submitted',
            'submitted_by_name' => 'Budi Client',
            'file_name' => 'logo_perusahaan.png',
        ],
    ]);

    $asset->refresh();
    expect($asset->status)->toBe('submitted');
    expect($asset->file_path)->not->toBeNull();
    expect($asset->file_name)->toBe('logo_perusahaan.png');
    expect($asset->submitted_by_name)->toBe('Budi Client');
    Storage::disk('public')->assertExists($asset->file_path);

    // Assert that a synced chat message was generated in project_messages
    $this->assertDatabaseHas('project_messages', [
        'project_id' => $this->project->id,
        'client_name' => 'Budi Client',
    ]);
});

test('client can submit asset with cloud url and notes', function () {
    $this->get(route('client.portal.show', $this->project->share_token));

    $asset = $this->project->assetRequirements()->first();

    $response = $this->post(
        route('client.portal.assets.submit', [$this->project->share_token, $asset->id]),
        [
            'client_name' => 'Siti Rahma',
            'external_url' => 'https://drive.google.com/drive/folders/123456789abcdef',
            'client_notes' => 'Folder berisi seluruh aset gambar dan video pendukung.',
        ],
        ['X-Requested-With' => 'XMLHttpRequest']
    );

    $response->assertStatus(200);
    $asset->refresh();

    expect($asset->status)->toBe('submitted');
    expect($asset->external_url)->toBe('https://drive.google.com/drive/folders/123456789abcdef');
    expect($asset->client_notes)->toContain('Folder berisi seluruh aset gambar');
});

test('team or client can add new custom asset requirement to adjust per project', function () {
    $response = $this->post(
        route('client.portal.assets.store', $this->project->share_token),
        [
            'title' => 'Video Profil Perusahaan 4K',
            'category' => 'Video',
            'description' => 'Durasi 60 detik format MP4 untuk hero landing page.',
            'is_mandatory' => 1,
        ],
        ['X-Requested-With' => 'XMLHttpRequest']
    );

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'asset' => [
            'title' => 'Video Profil Perusahaan 4K',
            'category' => 'Video',
            'is_mandatory' => true,
        ],
    ]);

    $this->assertDatabaseHas('project_asset_requirements', [
        'project_id' => $this->project->id,
        'title' => 'Video Profil Perusahaan 4K',
        'category' => 'Video',
    ]);
});

test('team can toggle approval status on asset', function () {
    $this->get(route('client.portal.show', $this->project->share_token));

    $asset = $this->project->assetRequirements()->first();
    expect($asset->status)->toBe('pending');

    // First toggle -> approved
    $response = $this->actingAs($this->user)->post(
        route('client.portal.assets.toggle-approval', [$this->project->share_token, $asset->id]),
        [],
        ['X-Requested-With' => 'XMLHttpRequest']
    );

    $response->assertStatus(200);
    $asset->refresh();
    expect($asset->status)->toBe('approved');
    expect($asset->reviewed_by)->toBe($this->user->id);
    expect($asset->reviewed_at)->not->toBeNull();

    // Second toggle -> reverted back
    $response2 = $this->actingAs($this->user)->post(
        route('client.portal.assets.toggle-approval', [$this->project->share_token, $asset->id]),
        [],
        ['X-Requested-With' => 'XMLHttpRequest']
    );

    $response2->assertStatus(200);
    $asset->refresh();
    expect($asset->status)->toBe('pending');
});

test('asset requirement can be deleted', function () {
    $this->get(route('client.portal.show', $this->project->share_token));

    $asset = $this->project->assetRequirements()->first();
    $assetId = $asset->id;

    $response = $this->delete(
        route('client.portal.assets.delete', [$this->project->share_token, $asset->id]),
        [],
        ['X-Requested-With' => 'XMLHttpRequest']
    );

    $response->assertStatus(200);
    $this->assertDatabaseMissing('project_asset_requirements', [
        'id' => $assetId,
    ]);
});
