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
        Schema::create('profiles', function (Blueprint $table) {
            $table->char('id', length: 9)->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('fullname')->unique();
            $table->char('phone', length: 20);
            $table->char('current_city', length: 40)->nullable();
            $table->char('current_province', length: 50)->nullable();
            $table->string('email')->unique();
            $table->date('birthday');
            $table->json('tagline')->nullable();
            $table->json('description')->nullable();
            $table->json('philosophy')->nullable();
            $table->char('status', length: 30);
            $table->string('formal_photo')->nullable();
            $table->string('casual_photo')->nullable();
            $table->string('setup_image')->nullable();
            $table->json('resume')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
