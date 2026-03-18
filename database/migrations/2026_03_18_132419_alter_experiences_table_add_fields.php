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
        Schema::table('experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('experiences', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('experiences', 'company')) {
                $table->string('company')->after('user_id');
            }
            if (! Schema::hasColumn('experiences', 'role')) {
                $table->string('role')->after('company');
            }
            if (! Schema::hasColumn('experiences', 'location')) {
                $table->string('location')->nullable()->after('role');
            }
            if (! Schema::hasColumn('experiences', 'employment_type')) {
                $table->string('employment_type')->nullable()->after('location');
            }

            if (! Schema::hasColumn('experiences', 'summary')) {
                $table->text('summary')->nullable()->after('employment_type');
            }
            if (! Schema::hasColumn('experiences', 'highlights')) {
                $table->json('highlights')->nullable()->after('summary');
            }

            if (! Schema::hasColumn('experiences', 'started_on')) {
                $table->date('started_on')->nullable()->after('highlights');
            }
            if (! Schema::hasColumn('experiences', 'ended_on')) {
                $table->date('ended_on')->nullable()->after('started_on');
            }
            if (! Schema::hasColumn('experiences', 'is_current')) {
                $table->boolean('is_current')->default(false)->after('ended_on');
            }

            if (! Schema::hasColumn('experiences', 'company_url')) {
                $table->string('company_url')->nullable()->after('is_current');
            }
            if (! Schema::hasColumn('experiences', 'company_logo_path')) {
                $table->string('company_logo_path')->nullable()->after('company_url');
            }

            if (! Schema::hasColumn('experiences', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('company_logo_path');
            }
            if (! Schema::hasColumn('experiences', 'is_featured')) {
                $table->boolean('is_featured')->default(true)->after('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'company',
                'role',
                'location',
                'employment_type',
                'summary',
                'highlights',
                'started_on',
                'ended_on',
                'is_current',
                'company_url',
                'company_logo_path',
                'sort_order',
                'is_featured',
            ]);
        });
    }
};
