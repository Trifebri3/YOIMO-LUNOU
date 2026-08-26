<?php

namespace App\Services\AI;

use App\Contracts\AIProviderInterface;
use App\Support\AI\AIResponse;
use App\Exceptions\AIProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct(string $apiKey, string $model, ?string $baseUrl = null)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->baseUrl = $baseUrl ?: config('ai.providers.openai.base_url', 'https://api.openai.com/v1');
    }

    public function chat(array $payload): AIResponse
    {
        $messages = [];
        if (isset($payload['messages'])) {
            $messages = $payload['messages'];
        } elseif (isset($payload['message'])) {
            if (isset($payload['system'])) {
                $messages[] = ['role' => 'system', 'content' => $payload['system']];
            }
            $messages[] = ['role' => 'user', 'content' => $payload['message']];
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => $payload['temperature'] ?? 0.7,
                'max_tokens' => $payload['max_tokens'] ?? null,
            ]);

            if ($response->failed()) {
                $status = $response->status();
                $errBody = $response->json();
                $errMsg = $errBody['error']['message'] ?? 'OpenAI API request failed.';
                Log::error('OpenAI adapter error: ', ['status' => $status, 'error' => $errBody]);
                throw new AIProviderException($errMsg, $status);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            $inputTokens = $data['usage']['prompt_tokens'] ?? null;
            $outputTokens = $data['usage']['completion_tokens'] ?? null;
            $totalTokens = $data['usage']['total_tokens'] ?? null;

            return new AIResponse(
                content: $content,
                model: $this->model,
                provider: 'openai',
                inputTokens: $inputTokens,
                outputTokens: $outputTokens,
                totalTokens: $totalTokens,
                raw: $data
            );
        } catch (\Exception $e) {
            if ($e instanceof AIProviderException) {
                throw $e;
            }
            Log::error('OpenAI unexpected exception: ' . $e->getMessage());
            throw new AIProviderException('Connection failure or timeout to OpenAI: ' . $e->getMessage(), 500, $e);
        }
    }

    public function getModels(): array
    {
        return config('ai.providers.openai.models', []);
    }

    public function validateConnection(): bool
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->timeout(10)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => 'ping']
                ],
                'max_tokens' => 5
            ]);

            if ($response->failed()) {
                $err = $response->json();
                $msg = $err['error']['message'] ?? 'HTTP ' . $response->status() . ': ' . $response->body();
                throw new AIProviderException($msg, $response->status());
            }

            return true;
        } catch (\Exception $e) {
            if ($e instanceof AIProviderException) {
                throw $e;
            }
            throw new AIProviderException('Connection error: ' . $e->getMessage(), 500, $e);
        }
    }
}
