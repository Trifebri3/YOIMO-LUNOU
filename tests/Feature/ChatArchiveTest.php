<?php

use App\Models\ChatArchive;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->user = User::where('role', 'management')->first();
    $this->contact = User::where('role', 'user')->first();
});

test('user can archive and unarchive a personal contact chat', function () {
    // 1. Initially, no chat is archived
    $response = $this->actingAs($this->user)->get(route('chat.index'));
    $response->assertStatus(200);
    $response->assertSee($this->contact->name);

    // 2. Archive the chat
    $archiveResponse = $this->actingAs($this->user)->post(route('chat.toggle-archive'), [
        'user_id' => $this->contact->id,
    ]);
    $archiveResponse->assertRedirect(route('chat.index'));

    // Check database
    $archiveRecord = ChatArchive::where('user_id', $this->user->id)
        ->where('archived_user_id', $this->contact->id)
        ->first();
    expect($archiveRecord)->not->toBeNull();

    // 3. Verify it is hidden from active list
    $activeListResponse = $this->actingAs($this->user)->get(route('chat.index'));
    $activeListResponse->assertStatus(200);
    $activeListResponse->assertDontSee($this->contact->name);

    // 4. Verify it is shown in archived list
    $archivedListResponse = $this->actingAs($this->user)->get(route('chat.index', ['filter' => 'archived']));
    $archivedListResponse->assertStatus(200);
    $archivedListResponse->assertSee($this->contact->name);

    // 5. Unarchive the chat
    $unarchiveResponse = $this->actingAs($this->user)->post(route('chat.toggle-archive'), [
        'user_id' => $this->contact->id,
    ]);
    $unarchiveResponse->assertRedirect();

    // Verify it is removed from database
    $archiveRecordAfter = ChatArchive::where('user_id', $this->user->id)
        ->where('archived_user_id', $this->contact->id)
        ->first();
    expect($archiveRecordAfter)->toBeNull();
});
