<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. user_points
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('login_streak')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        // 2. user_point_logs
        Schema::create('user_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->string('source_type'); // e.g. daily_login, task_ontime, task_overdue, wellbeing_checkin
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('company_profile_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 3. user_awards
        Schema::create('user_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('award_type'); // e.g. si_paling_rajin_login, si_paling_tepat_waktu, si_paling_produktif
            $table->string('title');
            $table->date('issued_date');
            $table->string('share_token')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_awards');
        Schema::dropIfExists('user_point_logs');
        Schema::dropIfExists('user_points');
    }
};
