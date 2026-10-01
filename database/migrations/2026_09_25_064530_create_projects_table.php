<?php

use App\Enums\ProjectStatus;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->char('id', length: 9)->primary();
            $table->char('profile_id', length: 9);
            $table->char('portfolio_id', length: 9)->nullable();
            $table->char('title', length: 100)->unique();
            $table->char('slug')->unique();
            $table->string('type');
            $table->string('category');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->text('video')->nullable();
            $table->date('start_period');
            $table->date('finish_period')->nullable();
            $table->string('status')->default(ProjectStatus::Planning->value);
            $table->json('description')->nullable();
            $table->text('delay_reason')->nullable();
            $table->date('resume_date')->nullable();
            $table->json('tags')->nullable();
            $table->json('technology')->nullable();
            $table->string('source_code')->nullable();
            $table->string('demo_url')->nullable();
            $table->boolean('is_private')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('portfolio_id')->references('id')->on('portfolios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
