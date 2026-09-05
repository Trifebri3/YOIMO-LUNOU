<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Backfill slug untuk seluruh user yang sudah ada
        $users = User::all();
        $usedSlugs = [];

        foreach ($users as $u) {
            $baseSlug = Str::slug($u->name) ?: 'user-'.$u->id;
            $slug = $baseSlug;
            $counter = 1;

            while (in_array($slug, $usedSlugs) || User::where('slug', $slug)->where('id', '!=', $u->id)->exists()) {
                $counter++;
                $slug = $baseSlug.'-'.$counter;
            }

            $usedSlugs[] = $slug;
            $u->updateQuietly(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
