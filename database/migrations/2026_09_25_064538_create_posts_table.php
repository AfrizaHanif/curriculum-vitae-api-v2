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
        Schema::create('posts', function (Blueprint $table) {
            $table->char('id', length: 9)->primary();
            $table->char('profile_id', length: 9);
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('author');
            $table->json('tags')->nullable();
            $table->text('summary');
            $table->string('image')->nullable();
            $table->text('content');
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
