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
        DB::table('portfolios')->whereIn('type', ['Academic Project', 'Proyek Akademik'])->update(['type' => ProjectType::Academic->value]);
        DB::table('portfolios')->whereIn('type', ['Personal Project', 'Proyek Pribadi', 'Personal'])->update(['type' => ProjectType::Personal->value]);
        DB::table('portfolios')->whereIn('type', ['Commercial Project', 'Proyek Komersial', 'Commercial', 'Internal'])->update(['type' => ProjectType::Commercial->value]);
        DB::table('portfolios')->whereIn('type', ['Open Source Project', 'Open Source', 'Sumber Terbuka'])->update(['type' => ProjectType::OpenSource->value]);
        DB::table('portfolios')->whereIn('type', ['Freelance Project', 'Freelance', 'Pekerja Lepas'])->update(['type' => ProjectType::Freelance->value]);

        // 2. Change column definition to string with default value
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('type')->default(ProjectType::Academic->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert legacy values
        DB::table('portfolios')->where('type', ProjectType::Academic->value)->update(['type' => 'Proyek Akademik']);
        DB::table('portfolios')->where('type', ProjectType::Personal->value)->update(['type' => 'Proyek Pribadi']);
        DB::table('portfolios')->where('type', ProjectType::Commercial->value)->update(['type' => 'Proyek Komersial']);
        DB::table('portfolios')->where('type', ProjectType::OpenSource->value)->update(['type' => 'Sumber Terbuka']);
        DB::table('portfolios')->where('type', ProjectType::Freelance->value)->update(['type' => 'Pekerja Lepas']);

        // 2. Revert column definition back without default
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('type')->default(null)->change();
        });
    }
};
