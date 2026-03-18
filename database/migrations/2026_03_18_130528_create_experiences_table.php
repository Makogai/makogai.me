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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('company');
            $table->string('role');
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();

            $table->text('summary')->nullable();
            $table->json('highlights')->nullable();

            $table->date('started_on')->nullable();
            $table->date('ended_on')->nullable();
            $table->boolean('is_current')->default(false);

            $table->string('company_url')->nullable();
            $table->string('company_logo_path')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(true);

            $table->timestamps();

            $table->index(['is_current', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
