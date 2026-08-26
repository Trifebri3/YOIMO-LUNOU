<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send message via Fonnte API
     */
    public static function send(?string $target, string $message): bool
    {
        $token = env('FONNTE_TOKEN', 'Eeo4Pf43HnDcaeSQmSua');
        $defaultTarget = env('FONNTE_DEFAULT_TARGET', '6285862319524');

        // Clean target, fallback if empty
        $target = $target ? trim($target) : '';
        if (empty($target)) {
            $target = $defaultTarget;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->withoutVerifying()->asForm()->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);

            $json = $response->json();
            if ($response->successful() && ($json['status'] ?? false) == true) {
                Log::info("WhatsApp successfully sent to {$target}: " . substr($message, 0, 50));
                return true;
            }

            Log::error("Fonnte API error response for {$target}: " . json_encode($json));
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp service exception: " . $e->getMessage());
            return false;
        }
    }
}
