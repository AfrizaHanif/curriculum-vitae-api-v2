<?php

use App\Enums\PostCategory;
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
        // 1. Backfill and normalize legacy category values in database
        DB::table('posts')->where('category', 'Announcement')->update(['category' => PostCategory::Announcement->value]);
        DB::table('posts')->where('category', 'Tutorial')->update(['category' => PostCategory::Tutorial->value]);
        DB::table('posts')->where('category', 'Tech')->update(['category' => PostCategory::Tech->value]);
        DB::table('posts')->where('category', 'Career')->update(['category' => PostCategory::Career->value]);

        // 2. Change column definition to string with default value
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->default(PostCategory::Announcement->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert legacy values
        DB::table('posts')->where('category', PostCategory::Announcement->value)->update(['category' => 'Announcement']);
        DB::table('posts')->where('category', PostCategory::Tutorial->value)->update(['category' => 'Tutorial']);
        DB::table('posts')->where('category', PostCategory::Tech->value)->update(['category' => 'Tech']);
        DB::table('posts')->where('category', PostCategory::Career->value)->update(['category' => 'Career']);

        // 2. Revert column definition back without default
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->default(null)->change();
        });
    }
};
