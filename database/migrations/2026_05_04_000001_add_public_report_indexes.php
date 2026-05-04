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
        Schema::table('responses', function (Blueprint $table) {
            $table->index(['institution_id', 'created_at'], 'responses_institution_created_at_idx');
            $table->index('created_at', 'responses_created_at_idx');
            $table->index(['education_id', 'created_at'], 'responses_education_created_at_idx');
            $table->index(['occupation_id', 'created_at'], 'responses_occupation_created_at_idx');
            $table->index(['gender', 'created_at'], 'responses_gender_created_at_idx');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->index(['response_id', 'question_id'], 'answers_response_question_idx');
            $table->index(['question_id', 'response_id'], 'answers_question_response_idx');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index('unsur_id', 'questions_unsur_id_idx');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->index('slug', 'institutions_slug_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropIndex('institutions_slug_idx');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_unsur_id_idx');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('answers_response_question_idx');
            $table->dropIndex('answers_question_response_idx');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('responses_institution_created_at_idx');
            $table->dropIndex('responses_created_at_idx');
            $table->dropIndex('responses_education_created_at_idx');
            $table->dropIndex('responses_occupation_created_at_idx');
            $table->dropIndex('responses_gender_created_at_idx');
        });
    }
};
