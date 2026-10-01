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
        Schema::create('features', function (Blueprint $table) {
            $table->char('id', length: 7)->primary();
            $table->string('featureable_type');
            $table->char('featureable_id', length: 9);
            $table->index(['featureable_type', 'featureable_id']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('progress')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
