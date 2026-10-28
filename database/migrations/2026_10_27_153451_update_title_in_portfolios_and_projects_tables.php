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
        // 1. Drop unique index and expand title column to text on portfolios
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropUnique(['title']);
            $table->text('title')->change();
        });

        // 2. Backfill existing records: convert plain text title to JSON on portfolios
        $portfolios = DB::table('portfolios')->get(['id', 'title']);
        foreach ($portfolios as $portfolio) {
            if (! empty($portfolio->title)) {
                json_decode((string) $portfolio->title);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    DB::table('portfolios')->where('id', $portfolio->id)->update([
                        'title' => json_encode([
                            'id' => (string) $portfolio->title,
                            'en' => (string) $portfolio->title,
                        ], JSON_UNESCAPED_UNICODE),
                    ]);
                }
            }
        }

        // 3. Convert title column to json on portfolios
        Schema::table('portfolios', function (Blueprint $table) {
            $table->json('title')->change();
        });

        // 4. Drop unique index and expand title column to text on projects
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['title']);
            $table->text('title')->change();
        });

        // 5. Backfill existing records: convert plain text title to JSON on projects
        $projects = DB::table('projects')->get(['id', 'title']);
        foreach ($projects as $project) {
            if (! empty($project->title)) {
                json_decode((string) $project->title);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    DB::table('projects')->where('id', $project->id)->update([
                        'title' => json_encode([
                            'id' => (string) $project->title,
                            'en' => (string) $project->title,
                        ], JSON_UNESCAPED_UNICODE),
                    ]);
                }
            }
        }

        // 6. Convert title column to json on projects
        Schema::table('projects', function (Blueprint $table) {
            $table->json('title')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Expand title column to text on projects
        Schema::table('projects', function (Blueprint $table) {
            $table->text('title')->change();
        });

        // 2. Convert JSON title back to string on projects
        $projects = DB::table('projects')->get(['id', 'title']);
        foreach ($projects as $project) {
            if (! empty($project->title)) {
                $decoded = json_decode((string) $project->title, true);
                if (is_array($decoded)) {
                    $textTitle = $decoded['id'] ?? $decoded['en'] ?? (string) reset($decoded);
                    DB::table('projects')->where('id', $project->id)->update([
                        'title' => substr($textTitle, 0, 100),
                    ]);
                }
            }
        }

        // 3. Revert title column on projects
        Schema::table('projects', function (Blueprint $table) {
            $table->char('title', length: 100)->unique()->change();
        });

        // 4. Expand title column to text on portfolios
        Schema::table('portfolios', function (Blueprint $table) {
            $table->text('title')->change();
        });

        // 5. Convert JSON title back to string on portfolios
        $portfolios = DB::table('portfolios')->get(['id', 'title']);
        foreach ($portfolios as $portfolio) {
            if (! empty($portfolio->title)) {
                $decoded = json_decode((string) $portfolio->title, true);
                if (is_array($decoded)) {
                    $textTitle = $decoded['id'] ?? $decoded['en'] ?? (string) reset($decoded);
                    DB::table('portfolios')->where('id', $portfolio->id)->update([
                        'title' => substr($textTitle, 0, 255),
                    ]);
                }
            }
        }

        // 6. Revert title column on portfolios
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('title')->unique()->change();
        });
    }
};
