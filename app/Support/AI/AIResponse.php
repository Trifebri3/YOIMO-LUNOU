<?php

namespace App\Support\AI;

class AIResponse
{
    public function __construct(
        public string $content,
        public ?string $model = null,
        public ?string $provider = null,
        public ?int $inputTokens = null,
        public ?int $outputTokens = null,
        public ?int $totalTokens = null,
        public array $raw = [],
    ) {}
}
