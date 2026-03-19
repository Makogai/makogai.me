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
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'source')) {
                $table->string('source')->default('manual')->after('user_id')->index();
            }

            if (! Schema::hasColumn('activities', 'external_id')) {
                $table->string('external_id')->nullable()->after('source')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'external_id')) {
                $table->dropColumn('external_id');
            }

            if (Schema::hasColumn('activities', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
