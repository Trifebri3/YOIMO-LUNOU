<?php

use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->user = User::where('role', 'management')->first();

    $this->project = Project::create([
        'name' => 'MORE Barbershop System',
        'category' => 'Web Development',
        'status' => 'In Progress',
        'created_by' => $this->user->id,
        'share_token' => Str::random(32),
    ]);
});

test('guest client can view portal and see existing project messages', function () {
    ProjectMessage::create([
        'project_id' => $this->project->id,
        'sender_id' => $this->user->id,
        'message' => 'Halo dari tim pengembang!',
        'message_type' => 'chat',
        'is_read' => true,
    ]);

    $response = $this->get(route('client.portal.show', $this->project->share_token));
    $response->assertStatus(200);
    $response->assertSee('Halo dari tim pengembang!');
    $response->assertSee('Ruang Diskusi', false);
});

test('guest client can send chat message with client name', function () {
    $response = $this->post(route('client.portal.messages.send', $this->project->share_token), [
        'client_name' => 'Budi Santoso',
        'message' => 'Mohon update progres modul autentikasi ya.',
        'message_type' => 'chat',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('project_messages', [
        'project_id' => $this->project->id,
        'client_name' => 'Budi Santoso',
        'message' => 'Mohon update progres modul autentikasi ya.',
        'message_type' => 'chat',
        'sender_id' => null,
    ]);
});

test('guest client can submit kendala with screenshot image', function () {
    Storage::fake('public');

    $fakeImage = UploadedFile::fake()->image('kendala_login.png', 640, 480);

    $response = $this->post(route('client.portal.messages.send', $this->project->share_token), [
        'client_name' => 'Budi Santoso',
        'message' => 'Ada error saat klik tombol simpan pada halaman profil.',
        'message_type' => 'kendala',
        'file' => $fakeImage,
    ], ['X-Requested-With' => 'XMLHttpRequest']);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => [
            'sender_name' => 'Budi Santoso',
            'message_type' => 'kendala',
            'is_image' => true,
        ],
    ]);

    $savedMsg = ProjectMessage::where('project_id', $this->project->id)
        ->where('message_type', 'kendala')
        ->first();

    expect($savedMsg)->not->toBeNull();
    expect($savedMsg->attachment_file)->not->toBeNull();
    expect($savedMsg->is_image)->toBeTrue();
    Storage::disk('public')->assertExists($savedMsg->attachment_file);
});

test('authenticated user sends message with user identity', function () {
    $response = $this->actingAs($this->user)->post(route('client.portal.messages.send', $this->project->share_token), [
        'message' => 'Baik pak, kami periksa kendalanya sekarang.',
        'message_type' => 'chat',
    ], ['X-Requested-With' => 'XMLHttpRequest']);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => [
            'sender_id' => $this->user->id,
            'sender_name' => $this->user->name,
            'is_team' => true,
            'is_me' => true,
        ],
    ]);

    $this->assertDatabaseHas('project_messages', [
        'project_id' => $this->project->id,
        'sender_id' => $this->user->id,
        'message' => 'Baik pak, kami periksa kendalanya sekarang.',
    ]);
});

test('fetch messages polling endpoint returns new messages', function () {
    $msg = ProjectMessage::create([
        'project_id' => $this->project->id,
        'sender_id' => $this->user->id,
        'message' => 'Pesan polling real-time!',
        'message_type' => 'chat',
        'is_read' => false,
    ]);

    $response = $this->get(route('client.portal.messages.fetch', [
        'token' => $this->project->share_token,
        'last_id' => $msg->id - 1,
    ]));

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'id' => $msg->id,
        'message' => 'Pesan polling real-time!',
    ]);
});

test('messages from portal sync directly into internal whatsapp chat center', function () {
    ProjectMessage::create([
        'project_id' => $this->project->id,
        'client_name' => 'Klien Hebat',
        'sender_id' => null,
        'message' => 'Apakah fitur export PDF sudah selesai?',
        'message_type' => 'question',
        'is_read' => false,
    ]);

    // Authenticated team member visits internal chat center
    $response = $this->actingAs($this->user)->get(route('chat.index', ['project_id' => $this->project->id]));
    $response->assertStatus(200);
    $response->assertSee('Apakah fitur export PDF sudah selesai?');
    $response->assertSee('Klien Hebat');
});

test('legacy submitQuestion syncs into project_messages', function () {
    $response = $this->post(route('client.portal.ask', $this->project->share_token), [
        'client_name' => 'Pak Tri',
        'question' => 'Kapan jadwal rilis versi 2.0?',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('project_client_questions', [
        'project_id' => $this->project->id,
        'client_name' => 'Pak Tri',
        'question' => 'Kapan jadwal rilis versi 2.0?',
    ]);
    $this->assertDatabaseHas('project_messages', [
        'project_id' => $this->project->id,
        'message' => 'Kapan jadwal rilis versi 2.0?',
        'message_type' => 'question',
    ]);
});

test('can toggle resolution of kendala from portal with note and resolver name', function () {
    $msg = ProjectMessage::create([
        'project_id' => $this->project->id,
        'sender_id' => null,
        'client_name' => 'Klien Hebat',
        'message' => 'Ada kendala tombol submit tidak merespon di mobile browser.',
        'message_type' => 'kendala',
        'is_resolved' => false,
    ]);

    $response = $this->post(route('client.portal.messages.toggle-resolution', [
        'token' => $this->project->share_token,
        'message' => $msg->id,
    ]), [
        'is_resolved' => true,
        'resolver_name' => 'Tim Pengembang',
        'resolution_note' => 'Sudah diperbaiki event listener touch-nya pada patch v1.2.',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'data' => [
            'id' => $msg->id,
            'is_resolved' => true,
            'resolver_name' => 'Tim Pengembang',
            'resolution_note' => 'Sudah diperbaiki event listener touch-nya pada patch v1.2.',
        ],
    ]);

    $this->assertDatabaseHas('project_messages', [
        'id' => $msg->id,
        'is_resolved' => true,
        'resolver_name' => 'Tim Pengembang',
        'resolution_note' => 'Sudah diperbaiki event listener touch-nya pada patch v1.2.',
    ]);
});

test('can reopen kendala from portal', function () {
    $msg = ProjectMessage::create([
        'project_id' => $this->project->id,
        'sender_id' => null,
        'client_name' => 'Klien Hebat',
        'message' => 'Kendala sebelumnya muncul kembali setelah update.',
        'message_type' => 'kendala',
        'is_resolved' => true,
        'resolver_name' => 'Budi Santoso',
        'resolution_note' => 'Penyelesaian sementara',
    ]);

    $response = $this->post(route('client.portal.messages.toggle-resolution', [
        'token' => $this->project->share_token,
        'message' => $msg->id,
    ]), [
        'is_resolved' => false,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'data' => [
            'id' => $msg->id,
            'is_resolved' => false,
            'resolver_name' => null,
            'resolution_note' => null,
        ],
    ]);

    $this->assertDatabaseHas('project_messages', [
        'id' => $msg->id,
        'is_resolved' => false,
        'resolution_note' => null,
    ]);
});

test('authenticated team member can toggle resolution of kendala from chat center', function () {
    $msg = ProjectMessage::create([
        'project_id' => $this->project->id,
        'sender_id' => null,
        'client_name' => 'Klien Hebat',
        'message' => 'Laporan error 500 saat import excel.',
        'message_type' => 'kendala',
        'is_resolved' => false,
    ]);

    $response = $this->actingAs($this->user)->post(route('chat.messages.toggle-resolution', $msg->id), [
        'is_resolved' => true,
        'resolution_note' => 'Format kolom header sudah disesuaikan dan memory limit dinaikkan.',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'data' => [
            'id' => $msg->id,
            'is_resolved' => true,
            'resolver_name' => $this->user->name,
            'resolution_note' => 'Format kolom header sudah disesuaikan dan memory limit dinaikkan.',
        ],
    ]);

    $this->assertDatabaseHas('project_messages', [
        'id' => $msg->id,
        'is_resolved' => true,
        'resolved_by' => $this->user->id,
        'resolver_name' => $this->user->name,
        'resolution_note' => 'Format kolom header sudah disesuaikan dan memory limit dinaikkan.',
    ]);
});
