<?php

use App\Enums\EducationStatus;
use App\Enums\EducationType;
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
        Schema::create('education', function (Blueprint $table) {
            $table->char('id', length: 7)->primary();
            $table->char('profile_id', length: 9);
            $table->string('institution');
            $table->string('type')->default(EducationType::Formal->value);
            $table->string('address')->nullable();
            $table->char('degree', length: 10);
            $table->char('major', length: 50);
            $table->double('gpa')->nullable();
            $table->string('status')->default(EducationStatus::Graduated->value);
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
        Schema::dropIfExists('education');
    }
};
