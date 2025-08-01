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
        
        Schema::table('educations', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('level');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });

        Schema::table('occupations', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('type');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('text');
        });

        Schema::table('unsurs', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('educations', fn (Blueprint $table) => $table->dropColumn('slug'));
        Schema::table('institutions', fn (Blueprint $table) => $table->dropColumn('slug'));
        Schema::table('occupations', fn (Blueprint $table) => $table->dropColumn('slug'));
        Schema::table('questions', fn (Blueprint $table) => $table->dropColumn('slug'));
        Schema::table('unsurs', fn (Blueprint $table) => $table->dropColumn('slug'));
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn('slug'));
    }
};
