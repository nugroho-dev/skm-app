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
        Schema::create('responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            //$table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->integer('age');
            $table->foreignUuid('occupation_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('institution_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('education_id')->constrained('educations')->onDelete('cascade');
            //$table->foreignId('education_id')->constrained('educations');
            //$table->foreignId('occupation_id')->constrained('occupations');
            //$table->foreignId('institution_id')->constrained('institutions');
            //$table->foreignId('service_id')->constrained('services');
            $table->text('suggestion')->nullable();
            //$table->date('survey_date');
            //$table->time('survey_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
