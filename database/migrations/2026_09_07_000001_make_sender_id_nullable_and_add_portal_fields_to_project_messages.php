<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_messages', function (Blueprint $table) {
            // Drop existing foreign key so we can change sender_id
            $table->dropForeign(['sender_id']);
        });

        Schema::table('project_messages', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable()->change();
            $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();

            $table->string('client_name')->nullable()->after('sender_id');
            $table->string('message_type', 30)->default('chat')->after('message'); // chat, kendala, question
        });

        // Migrate existing project_client_questions into project_messages
        if (Schema::hasTable('project_client_questions')) {
            $existingQuestions = DB::table('project_client_questions')->get();
            foreach ($existingQuestions as $q) {
                // Insert the question
                $messageId = DB::table('project_messages')->insertGetId([
                    'sender_id' => null,
                    'recipient_id' => null,
                    'project_id' => $q->project_id,
                    'client_name' => $q->client_name ?? 'Klien',
                    'message' => $q->question,
                    'message_type' => 'question',
                    'attachment_file' => null,
                    'attachment_name' => null,
                    'is_read' => true,
                    'created_at' => $q->created_at ?? now(),
                    'updated_at' => $q->created_at ?? now(),
                ]);

                // If question was answered, insert answer message as team response
                if (! empty($q->answer)) {
                    DB::table('project_messages')->insert([
                        'sender_id' => $q->answered_by,
                        'recipient_id' => null,
                        'project_id' => $q->project_id,
                        'client_name' => null,
                        'message' => $q->answer,
                        'message_type' => 'chat',
                        'attachment_file' => null,
                        'attachment_name' => null,
                        'is_read' => true,
                        'created_at' => $q->answered_at ?? $q->updated_at ?? now(),
                        'updated_at' => $q->answered_at ?? $q->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_messages', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'message_type']);
            $table->dropForeign(['sender_id']);
        });

        Schema::table('project_messages', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable(false)->change();
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
