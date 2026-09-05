<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellbeing_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('checkin_date');
            $table->string('mood')->nullable();
            $table->integer('energy')->default(3);
            $table->integer('mental_load')->default(3);
            $table->integer('rest_condition')->default(3);
            $table->text('thoughts')->nullable();
            $table->timestamps();

            // Unique checkin per user per date
            $table->unique(['user_id', 'checkin_date']);
        });

        Schema::create('wellbeing_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('feeling')->nullable();
            $table->text('today_event')->nullable();
            $table->text('gratitude')->nullable();
            $table->text('let_go')->nullable();
            $table->text('improvement')->nullable();
            $table->timestamps();
        });

        Schema::create('wellbeing_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('category'); // Belajar, Membaca, Olahraga, Istirahat, Family time, Personal project
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        Schema::create('wellbeing_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('week_number'); // e.g. 2026-W34
            $table->text('what_went_well')->nullable();
            $table->text('what_was_exhausting')->nullable();
            $table->text('what_to_change')->nullable();
            $table->text('what_proud_of')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('wellbeing_left_thoughts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('thought');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellbeing_left_thoughts');
        Schema::dropIfExists('wellbeing_reflections');
        Schema::dropIfExists('wellbeing_goals');
        Schema::dropIfExists('wellbeing_journals');
        Schema::dropIfExists('wellbeing_checkins');
    }
};
