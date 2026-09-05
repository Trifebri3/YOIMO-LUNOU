<?php

use App\Models\AISetting;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Support\Facades\Http;

test('ai setting encryption works', function () {
    $user = User::factory()->create();

    $setting = AISetting::create([
        'user_id' => $user->id,
        'provider' => 'openrouter',
        'name' => 'My OpenRouter',
        'api_key' => 'super-secret-key-123',
        'model' => 'google/gemini-2.5-flash-lite',
    ]);

    $this->assertDatabaseHas('ai_settings', [
        'id' => $setting->id,
    ]);

    // Check decrypted attribute matches original
    $this->assertEquals('super-secret-key-123', $setting->api_key);
});

test('ai service chat route works with mock', function () {
    $user = User::factory()->create();

    // Create active setting for user
    AISetting::create([
        'user_id' => $user->id,
        'provider' => 'openai',
        'name' => 'My OpenAI',
        'api_key' => 'secret-openai-key',
        'model' => 'gpt-4o-mini',
        'is_active' => true,
    ]);

    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Hello from mock OpenAI!',
                    ],
                ],
            ],
            'usage' => [
                'prompt_tokens' => 10,
                'completion_tokens' => 15,
                'total_tokens' => 25,
            ],
        ], 200),
    ]);

    $this->actingAs($user);

    $aiService = app(AIService::class);
    $response = $aiService->chat(['message' => 'Hi AI']);

    $this->assertEquals('Hello from mock OpenAI!', $response->content);
    $this->assertEquals('openai', $response->provider);
    $this->assertEquals('gpt-4o-mini', $response->model);
    $this->assertEquals(25, $response->totalTokens);
});

test('connection validation testing works', function () {
    $user = User::factory()->create();

    Http::fake([
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([], 200),
    ]);

    $response = $this->actingAs($user)->post(route('ai-settings.test'), [
        'provider' => 'openrouter',
        'model' => 'google/gemini-2.5-flash-lite',
        'api_key' => 'test-openrouter-key',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Koneksi berhasil terhubung.',
    ]);
});
