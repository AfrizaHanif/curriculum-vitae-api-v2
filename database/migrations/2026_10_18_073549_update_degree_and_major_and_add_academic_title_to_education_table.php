<?php

use App\Enums\EducationDegree;
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
        // 1. Temporarily expand major column to text so that JSON strings of any length can be safely written
        Schema::table('education', function (Blueprint $table) {
            $table->text('major')->change();
        });

        // 2. Backfill existing records: normalize legacy degree codes and convert plain text major to JSON
        $rows = DB::table('education')->get(['id', 'degree', 'major']);
        foreach ($rows as $row) {
            $updates = [];

            if ($row->degree === 'S1') {
                $updates['degree'] = EducationDegree::Bachelor->value;
            } elseif ($row->degree === 'S2') {
                $updates['degree'] = EducationDegree::Master->value;
            } elseif ($row->degree === 'S3') {
                $updates['degree'] = EducationDegree::Doctorate->value;
            } elseif ($row->degree === 'D3' || $row->degree === 'D4') {
                $updates['degree'] = EducationDegree::Associate->value;
            }

            if (! empty($row->major)) {
                json_decode((string) $row->major);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $updates['major'] = json_encode([
                        'id' => (string) $row->major,
                        'en' => (string) $row->major,
                    ], JSON_UNESCAPED_UNICODE);
                }
            }

            if (! empty($updates)) {
                DB::table('education')->where('id', $row->id)->update($updates);
            }
        }

        // 3. Apply schema modifications for degree, academic_title, and major
        Schema::table('education', function (Blueprint $table) {
            $table->string('degree')->default(EducationDegree::Bachelor->value)->change();
            if (! Schema::hasColumn('education', 'academic_title')) {
                $table->string('academic_title', length: 20)->nullable()->after('degree');
            }
            $table->json('major')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Change column to text first so it can accept non-JSON string values
        Schema::table('education', function (Blueprint $table) {
            $table->text('major')->change();
        });

        // 2. Convert JSON major back to string
        $rows = DB::table('education')->get(['id', 'major']);
        foreach ($rows as $row) {
            if (! empty($row->major)) {
                $decoded = json_decode((string) $row->major, true);
                if (is_array($decoded)) {
                    $textMajor = $decoded['id'] ?? $decoded['en'] ?? (string) reset($decoded);
                    DB::table('education')->where('id', $row->id)->update([
                        'major' => substr($textMajor, 0, 50),
                    ]);
                }
            }
        }

        // 3. Revert schema changes
        Schema::table('education', function (Blueprint $table) {
            if (Schema::hasColumn('education', 'academic_title')) {
                $table->dropColumn('academic_title');
            }
            $table->char('degree', length: 10)->change();
            $table->char('major', length: 50)->change();
        });
    }
};
