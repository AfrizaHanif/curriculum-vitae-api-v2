<?php

use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
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
        Schema::create('experiences', function (Blueprint $table) {
            $table->char('id', length: 7)->primary();
            $table->char('profile_id', length: 9);
            $table->string('title');
            $table->string('company');
            $table->string('type')->default(ExperienceType::FullTime->value);
            $table->string('address')->nullable();
            $table->string('status')->default(ExperienceStatus::Finished->value);
            $table->date('start_period');
            $table->date('finish_period')->nullable();
            $table->json('description')->nullable();
            $table->char('latitude', length: 15)->nullable();
            $table->char('longitude', length: 15)->nullable();
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
        Schema::dropIfExists('experiences');
    }
};
