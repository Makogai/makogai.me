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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->longText('content_markdown')->nullable();
            $table->longText('content_html')->nullable();

            $table->string('cover_image_path')->nullable();
            $table->json('gallery')->nullable();

            $table->json('tech_stack')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('demo_url')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable()->index();

            $table->string('seo_title')->nullable();
            $table->string('seo_description', 280)->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['published_at', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
