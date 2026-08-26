<?php

namespace App\Enums;

enum AIProviderEnum: string
{
    case OPENAI = 'openai';
    case GEMINI = 'gemini';
    case OPENROUTER = 'openrouter';
}
