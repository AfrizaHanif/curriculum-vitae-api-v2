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
        Schema::create('case_studies', function (Blueprint $table) {
            $table->char('id', length: 9)->primary();
            $table->char('portfolio_id', length: 9);
            $table->string('role');
            $table->json('problems');
            $table->json('goals');
            $table->json('responsibilities')->nullable();
            $table->json('diagrams')->nullable();
            $table->json('solutions')->nullable();
            $table->json('benefits')->nullable();
            $table->json('results')->nullable();
            $table->json('process')->nullable();
            $table->json('challenges')->nullable();
            $table->json('lessons')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('portfolio_id')->references('id')->on('portfolios')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
