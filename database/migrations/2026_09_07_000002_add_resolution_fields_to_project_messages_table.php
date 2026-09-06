<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_messages', function (Blueprint $table) {
            $table->boolean('is_resolved')->default(false)->after('message_type');
            $table->timestamp('resolved_at')->nullable()->after('is_resolved');
            $table->foreignId('resolved_by')->nullable()->after('resolved_at')->constrained('users')->nullOnDelete();
            $table->string('resolver_name')->nullable()->after('resolved_by');
            $table->text('resolution_note')->nullable()->after('resolver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_messages', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn([
                'is_resolved',
                'resolved_at',
                'resolved_by',
                'resolver_name',
                'resolution_note',
            ]);
        });
    }
};
