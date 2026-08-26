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
        Schema::table('wellbeing_journals', function (Blueprint $table) {
            $table->text('raw_content')->nullable()->after('user_id');
            $table->text('analysis')->nullable()->after('improvement');
            $table->text('appreciation')->nullable()->after('analysis');
            $table->json('categories')->nullable()->after('appreciation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wellbeing_journals', function (Blueprint $table) {
            $table->dropColumn(['raw_content', 'analysis', 'appreciation', 'categories']);
        });
    }
};
