<?php

use App\Enums\CertificateType;
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
        DB::table('certificates')->where('type', 'Kursus')->update(['type' => CertificateType::Course->value]);
        DB::table('certificates')->where('type', 'Pelatihan')->update(['type' => CertificateType::Training->value]);
        DB::table('certificates')->where('type', 'Magang')->update(['type' => CertificateType::Internship->value]);
        DB::table('certificates')->where('type', 'Sertifikasi')->update(['type' => CertificateType::Certification->value]);

        // 2. Change column definition to string with default value
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('type')->default(CertificateType::Course->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert legacy values
        DB::table('certificates')->where('type', CertificateType::Course->value)->update(['type' => 'Kursus']);
        DB::table('certificates')->where('type', CertificateType::Training->value)->update(['type' => 'Pelatihan']);
        DB::table('certificates')->where('type', CertificateType::Internship->value)->update(['type' => 'Magang']);
        DB::table('certificates')->where('type', CertificateType::Certification->value)->update(['type' => 'Sertifikasi']);

        // 2. Revert column definition back to char(20) without default
        Schema::table('certificates', function (Blueprint $table) {
            $table->char('type', length: 20)->change();
        });
    }
};
