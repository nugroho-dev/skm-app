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
       $tables = [
            'users',
            'mpps',
            'institution_groups',
            'institutions',
            'educations',
            'occupations',
            'services',
            'questions',
            'responses',
            'answers',
            'unsurs',
            'questions'
        ];

        foreach ($tables as $table) {
           if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'mpps',
            'institution_groups',
            'institutions',
            'educations',
            'occupations',
            'services',
            'questions',
            'responses',
            'answers',
            'unsurs',
            'questions'
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }
};
