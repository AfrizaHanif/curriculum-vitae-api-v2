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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->char('id', length: 9)->primary();
            $table->char('profile_id', length: 9);
            // $table->char('parent_id', length: 9)->nullable();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('type');
            $table->string('category');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->text('video')->nullable();
            $table->date('start_period');
            $table->date('finish_period');
            $table->json('description')->nullable();
            $table->json('tags')->nullable();
            $table->json('technology')->nullable();
            $table->json('repositories')->nullable();
            $table->string('demo_url')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            // $table->foreign('parent_id')->references('id')->on('portfolios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
