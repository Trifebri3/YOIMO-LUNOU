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
        if (! Schema::hasTable('company_profile_user')) {
            Schema::create('company_profile_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('role')->default('user');
                $table->timestamps();

                $table->unique(['company_profile_id', 'user_id']);
            });

            // Backfill existing user-company associations
            $existingUsers = DB::table('users')->whereNotNull('company_profile_id')->get();
            foreach ($existingUsers as $u) {
                DB::table('company_profile_user')->insertOrIgnore([
                    'company_profile_id' => $u->company_profile_id,
                    'user_id' => $u->id,
                    'role' => $u->role ?? 'user',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profile_user');
    }
};
