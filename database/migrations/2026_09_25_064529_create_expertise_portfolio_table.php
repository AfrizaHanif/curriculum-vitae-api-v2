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
        Schema::create('expertise_portfolio', function (Blueprint $table) {
            $table->char('expertise_id', length: 9);
            $table->char('portfolio_id', length: 9);

            $table->primary(['expertise_id', 'portfolio_id']);

            $table->foreign('expertise_id')->references('id')->on('expertises')->cascadeOnDelete();
            $table->foreign('portfolio_id')->references('id')->on('portfolios')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expertise_portfolio');
    }
};
