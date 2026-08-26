<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Portofolio Showcase Settings
            $table->boolean('is_showcased')->default(false)->after('current_stage'); // Toggle tampil di web publik
            $table->string('project_cover')->nullable()->after('is_showcased');
            $table->string('client_logo')->nullable()->after('project_cover');
            $table->string('demo_url')->nullable()->after('client_logo');
            
            // Detail Profil Showcase Publik
            $table->text('short_description')->nullable()->after('expected_outputs');
            $table->text('solution_statement')->nullable()->after('short_description');
            $table->text('result_statement')->nullable()->after('solution_statement');
            $table->json('services_rendered')->nullable()->after('result_statement'); // Array: ["Web Development", "UI/UX", "IoT Integration"]
            $table->json('tech_stacks')->nullable()->after('services_rendered');       // Array: ["Laravel 13", "Tailwind CSS", "MQTT", "PostgreSQL"]
            $table->json('gallery_images')->nullable()->after('tech_stacks');          // Array: ["path/to/img1.png", "path/to/img2.png"]
            $table->json('client_testimonial')->nullable()->after('gallery_images');   // Array: {"author": "...", "role": "...", "content": "...", "rating": 5}
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'is_showcased', 'project_cover', 'client_logo', 'demo_url',
                'short_description', 'solution_statement', 'result_statement',
                'services_rendered', 'tech_stacks', 'gallery_images', 'client_testimonial'
            ]);
        });
    }
};