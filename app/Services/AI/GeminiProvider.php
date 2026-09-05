<?php

namespace App\Services\AI;

use App\Contracts\AIProviderInterface;
use App\Exceptions\AIProviderException;
use App\Support\AI\AIResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl;

    public function __construct(string $apiKey, string $model, ?string $baseUrl = null)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->baseUrl = $baseUrl ?: config('ai.providers.gemini.base_url', 'https://generativelanguage.googleapis.com');
    }

    public function chat(array $payload): AIResponse
    {
        $contents = [];
        $systemInstruction = null;

        if (isset($payload['messages'])) {
            foreach ($payload['messages'] as $msg) {
                if ($msg['role'] === 'system') {
                    $systemInstruction = [
                        'parts' => [
                            ['text' => $msg['content']],
                        ],
                    ];
                } else {
                    $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                    $contents[] = [
                        'role' => $role,
                        'parts' => [
                            ['text' => $msg['content']],
                        ],
                    ];
                }
            }
        } elseif (isset($payload['message'])) {
            if (isset($payload['system'])) {
                $systemInstruction = [
                    'parts' => [
                        ['text' => $payload['system']],
                    ],
                ];
            }
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $payload['message']],
                ],
            ];
        }

        $body = [
            'contents' => $contents,
        ];

        if ($systemInstruction) {
            $body['systemInstruction'] = $systemInstruction;
        }

        // Add options if specified
        $generationConfig = [];
        if (isset($payload['temperature'])) {
            $generationConfig['temperature'] = $payload['temperature'];
        }
        if (isset($payload['max_tokens'])) {
            $generationConfig['maxOutputTokens'] = $payload['max_tokens'];
        }
        if (! empty($generationConfig)) {
            $body['generationConfig'] = $generationConfig;
        }

        try {
            // Target v1beta API endpoint for Gemini
            $url = rtrim($this->baseUrl, '/').'/v1beta/models/'.$this->model.':generateContent?key='.$this->apiKey;

            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($url, $body);

            if ($response->failed()) {
                $status = $response->status();
                $errBody = $response->json();
                $errMsg = $errBody['error']['message'] ?? 'Gemini API request failed.';
                Log::error('Gemini adapter error: ', ['status' => $status, 'error' => $errBody]);
                throw new AIProviderException($errMsg, $status);
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $inputTokens = $data['usageMetadata']['promptTokenCount'] ?? null;
            $outputTokens = $data['usageMetadata']['candidatesTokenCount'] ?? null;
            $totalTokens = $data['usageMetadata']['totalTokenCount'] ?? null;

            return new AIResponse(
                content: $content,
                model: $this->model,
                provider: 'gemini',
                inputTokens: $inputTokens,
                outputTokens: $outputTokens,
                totalTokens: $totalTokens,
                raw: $data
            );
        } catch (\Exception $e) {
            if ($e instanceof AIProviderException) {
                throw $e;
            }
            Log::error('Gemini unexpected exception: '.$e->getMessage());
            throw new AIProviderException('Connection failure or timeout to Gemini: '.$e->getMessage(), 500, $e);
        }
    }

    public function getModels(): array
    {
        return config('ai.providers.gemini.models', []);
    }

    public function validateConnection(): bool
    {
        try {
            $url = rtrim($this->baseUrl, '/').'/v1beta/models/'.$this->model.':generateContent?key='.$this->apiKey;
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(10)
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => 'ping'],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 5,
                    ],
                ]);

            if ($response->failed()) {
                $err = $response->json();
                $msg = $err['error']['message'] ?? 'HTTP '.$response->status().': '.$response->body();
                throw new AIProviderException($msg, $response->status());
            }

            return true;
        } catch (\Exception $e) {
            if ($e instanceof AIProviderException) {
                throw $e;
            }
            throw new AIProviderException('Connection error: '.$e->getMessage(), 500, $e);
        }
    }
}
