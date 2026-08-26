<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ai_settings
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // openai, gemini, openrouter
            $table->string('name'); // Custom configuration name
            $table->text('api_key'); // Encrypted api key
            $table->string('model'); // Selected model ID
            $table->string('base_url')->nullable(); // Optional custom base url
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable(); // Optional extra options
            $table->timestamps();
        });

        // 2. ai_usages
        Schema::create('ai_usages', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('model');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('request_type')->nullable(); // e.g. chat, generate, evaluation
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->integer('total_tokens')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->string('status'); // success, error
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usages');
        Schema::dropIfExists('ai_settings');
    }
};
