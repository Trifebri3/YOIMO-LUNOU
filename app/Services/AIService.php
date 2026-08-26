<?php

namespace App\Services;

use App\Models\AISetting;
use App\Models\AIUsage;
use App\Support\AI\AIResponse;
use App\Exceptions\AIProviderException;
use App\Services\AI\OpenAIProvider;
use App\Services\AI\GeminiProvider;
use App\Services\AI\OpenRouterProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Instansiasi adapter provider berdasarkan model AISetting
     */
    public function makeProvider(AISetting $setting)
    {
        return match ($setting->provider) {
            'openai' => new OpenAIProvider($setting->api_key, $setting->model, $setting->base_url),
            'gemini' => new GeminiProvider($setting->api_key, $setting->model, $setting->base_url),
            'openrouter' => new OpenRouterProvider($setting->api_key, $setting->model, $setting->base_url),
            default => throw new AIProviderException("Provider [{$setting->provider}] tidak terdaftar atau tidak didukung.", 400),
        };
    }

    /**
     * Mengirim chat request ke provider aktif dengan fallback logis & auditing
     */
    public function chat(array $payload): AIResponse
    {
        // 1. Dapatkan provider aktif utama untuk user saat ini (bisa dipassing lewat payload)
        $userId = $payload['user_id'] ?? Auth::id();
        $activeSetting = AISetting::where('user_id', $userId)->where('is_active', true)->first();

        if (!$activeSetting) {
            // Fallback ke default global sistem menggunakan key env/config
            $activeSetting = $this->getGlobalDefaultSetting();
        }

        if (!$activeSetting) {
            throw new AIProviderException("Anda belum mengonfigurasi atau mengaktifkan AI Gateway pribadi Anda. Silakan buka menu Pengaturan AI.", 404);
        }

        $startTime = microtime(true);
        $providerInstance = $this->makeProvider($activeSetting);

        try {
            // Jalankan request utama
            $response = $providerInstance->chat($payload);
            $responseTime = round((microtime(true) - $startTime) * 1000);

            // Audit usage sukses
            $this->logUsage($activeSetting, $response, $responseTime, 'success');

            return $response;

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            // Audit usage gagal untuk main provider
            $this->logUsage($activeSetting, null, $responseTime, 'error', $e->getMessage());

            // Cek apakah fallback diaktifkan
            $extraSettings = $activeSetting->settings ?? [];
            if (!empty($extraSettings['enable_fallback']) && !empty($extraSettings['fallback_provider'])) {
                $fallbackProvider = $extraSettings['fallback_provider'];
                
                // Cari setting untuk fallback provider milik user ini
                $fallbackSetting = AISetting::where('user_id', $userId)->where('provider', $fallbackProvider)->first();
                
                // Jika tidak ada di DB, coba cari di global credentials
                if (!$fallbackSetting) {
                    $globalApiKey = null;
                    if ($fallbackProvider === 'gemini') {
                        $globalApiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
                    } else {
                        $globalApiKey = env(strtoupper($fallbackProvider) . '_API_KEY');
                    }

                    if ($globalApiKey) {
                        $models = config("ai.providers.{$fallbackProvider}.models", []);
                        $defaultModel = array_key_first($models) ?: ($fallbackProvider === 'gemini' ? 'gemini-3.6-flash' : '');
                        
                        $fallbackSetting = new AISetting([
                            'provider' => $fallbackProvider,
                            'model' => $defaultModel,
                            'base_url' => config("ai.providers.{$fallbackProvider}.base_url"),
                            'is_active' => false,
                        ]);
                        $fallbackSetting->api_key = $globalApiKey;
                    }
                }

                if ($fallbackSetting && $fallbackSetting->id !== $activeSetting->id) {
                    Log::warning("AI Main provider [{$activeSetting->provider}] gagal. Melakukan fallback ke [{$fallbackProvider}]. Error: " . $e->getMessage());

                    $fallbackStartTime = microtime(true);
                    try {
                        $fallbackInstance = $this->makeProvider($fallbackSetting);
                        $response = $fallbackInstance->chat($payload);
                        $fallbackTime = round((microtime(true) - $fallbackStartTime) * 1000);

                        // Audit usage sukses untuk fallback
                        $this->logUsage($fallbackSetting, $response, $fallbackTime, 'success');

                        return $response;
                    } catch (\Exception $fe) {
                        $fallbackTime = round((microtime(true) - $fallbackStartTime) * 1000);
                        $this->logUsage($fallbackSetting, null, $fallbackTime, 'error', $fe->getMessage());
                        
                        throw new AIProviderException("Main provider [{$activeSetting->provider}] & Fallback provider [{$fallbackProvider}] keduanya gagal: " . $fe->getMessage(), 500, $fe);
                    }
                }
            }

            // Jika tidak ada fallback, rethrow exception original
            if ($e instanceof AIProviderException) {
                throw $e;
            }
            throw new AIProviderException($e->getMessage(), 500, $e);
        }
    }

    /**
     * Dapatkan setting default sistem menggunakan credentials dari env/config
     */
    public function getGlobalDefaultSetting(): ?AISetting
    {
        $provider = config('ai.default_provider', 'gemini');
        $apiKey = null;

        // Cek provider secara berurutan: default, gemini, openai, openrouter
        $providersToCheck = array_unique([
            $provider,
            'gemini',
            'openai',
            'openrouter'
        ]);

        foreach ($providersToCheck as $p) {
            if ($p === 'gemini') {
                $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
            } else {
                $apiKey = env(strtoupper($p) . '_API_KEY');
            }

            if ($apiKey) {
                $provider = $p;
                break;
            }
        }

        if ($apiKey) {
            $models = config("ai.providers.{$provider}.models", []);
            $defaultModel = array_key_first($models) ?: ($provider === 'gemini' ? 'gemini-3.6-flash' : '');
            
            $setting = new AISetting([
                'provider' => $provider,
                'model' => $defaultModel,
                'base_url' => config("ai.providers.{$provider}.base_url"),
                'is_active' => true,
            ]);
            $setting->api_key = $apiKey;

            return $setting;
        }

        return null;
    }

    /**
     * Catat log audit penggunaan AI ke tabel ai_usages
     */
    protected function logUsage(AISetting $setting, ?AIResponse $response, int $responseTimeMs, string $status, ?string $errorMessage = null): void
    {
        try {
            AIUsage::create([
                'provider' => $setting->provider,
                'model' => $setting->model,
                'user_id' => Auth::id(),
                'request_type' => 'chat',
                'input_tokens' => $response?->inputTokens,
                'output_tokens' => $response?->outputTokens,
                'total_tokens' => $response?->totalTokens,
                'response_time_ms' => $responseTimeMs,
                'status' => $status,
                'error_message' => $errorMessage ? substr($errorMessage, 0, 500) : null,
            ]);
        } catch (\Exception $e) {
            // Gagal catat log usage tidak boleh menggagalkan flow utama
            Log::error("Gagal mencatat log usage AI: " . $e->getMessage());
        }
    }
}
