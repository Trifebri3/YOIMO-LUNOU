<?php

namespace App\Contracts;

use App\Support\AI\AIResponse;

interface AIProviderInterface
{
    /**
     * Kirim chat request ke AI provider
     */
    public function chat(array $payload): AIResponse;

    /**
     * Dapatkan daftar model yang didukung provider ini
     */
    public function getModels(): array;

    /**
     * Validasi kredensial koneksi ke provider
     */
    public function validateConnection(): bool;
}
