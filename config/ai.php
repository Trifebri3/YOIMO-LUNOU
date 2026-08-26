<?php

return [
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'gemini'),

    'providers' => [
        'openai' => [
            'base_url' => 'https://api.openai.com/v1',
            'models' => [
                'gpt-4o' => 'GPT-4o',
                'gpt-4o-mini' => 'GPT-4o Mini',
                'gpt-4' => 'GPT-4',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ],
        ],

        'gemini' => [
            'base_url' => 'https://generativelanguage.googleapis.com',
            'models' => [
                'gemini-3.6-flash' => 'Gemini 3.6 Flash',
                'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            ],
        ],

        'openrouter' => [
            'base_url' => 'https://openrouter.ai/api/v1',
            'models' => [
                'google/gemini-2.5-flash-lite' => 'Gemini 2.5 Flash Lite (OpenRouter)',
                'google/gemini-2.5-pro' => 'Gemini 2.5 Pro (OpenRouter)',
                'meta-llama/llama-3-8b-instruct:free' => 'Llama 3 8B Instruct Free',
                'mistralai/mistral-7b-instruct:free' => 'Mistral 7B Instruct Free',
                'openai/gpt-4o-mini' => 'GPT-4o Mini (OpenRouter)',
            ],
        ],
    ],
];
