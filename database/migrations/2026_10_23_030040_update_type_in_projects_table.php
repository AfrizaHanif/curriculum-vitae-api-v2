<?php

use App\Enums\ProjectType;
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
        // 1. Backfill and normalize legacy type values in database
        DB::table('projects')->whereIn('type', ['Personal Project', 'Proyek Pribadi'])->update(['type' => ProjectType::Personal->value]);
        DB::table('projects')->whereIn('type', ['Academic Project', 'Proyek Akademik'])->update(['type' => ProjectType::Academic->value]);
        DB::table('projects')->whereIn('type', ['Commercial Project', 'Proyek Komersial'])->update(['type' => ProjectType::Commercial->value]);
        DB::table('projects')->whereIn('type', ['Open Source Project', 'Open Source', 'Sumber Terbuka'])->update(['type' => ProjectType::OpenSource->value]);
        DB::table('projects')->whereIn('type', ['Freelance Project', 'Freelance', 'Pekerja Lepas'])->update(['type' => ProjectType::Freelance->value]);

        // 2. Change column definition to string with default value
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->default(ProjectType::Personal->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert legacy values
        DB::table('projects')->where('type', ProjectType::Personal->value)->update(['type' => 'Personal Project']);
        DB::table('projects')->where('type', ProjectType::Academic->value)->update(['type' => 'Academic Project']);
        DB::table('projects')->where('type', ProjectType::Commercial->value)->update(['type' => 'Commercial Project']);
        DB::table('projects')->where('type', ProjectType::OpenSource->value)->update(['type' => 'Open Source Project']);
        DB::table('projects')->where('type', ProjectType::Freelance->value)->update(['type' => 'Freelance Project']);

        // 2. Revert column definition back without default
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->default(null)->change();
        });
    }
};
