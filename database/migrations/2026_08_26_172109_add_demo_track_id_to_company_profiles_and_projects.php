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
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->foreignId('demo_track_id')
                ->nullable()
                ->after('manager_id')
                ->constrained('demo_tracks')
                ->cascadeOnDelete();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('demo_track_id')
                ->nullable()
                ->after('company_profile_id')
                ->constrained('demo_tracks')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['demo_track_id']);
            $table->dropColumn('demo_track_id');
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropForeign(['demo_track_id']);
            $table->dropColumn('demo_track_id');
        });
    }
};
