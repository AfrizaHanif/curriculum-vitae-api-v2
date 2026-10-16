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
        Schema::table('education', function (Blueprint $table) {
            $table->double('max_gpa')->nullable()->after('gpa');
        });

        // Backfill existing records that already have a GPA (defaulting to 4.0)
        DB::table('education')
            ->whereNotNull('gpa')
            ->whereNull('max_gpa')
            ->update(['max_gpa' => 4.0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            $table->dropColumn('max_gpa');
        });
    }
};
