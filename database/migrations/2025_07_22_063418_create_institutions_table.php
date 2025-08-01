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
        Schema::create('institutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('institution_group_id')->constrained()->onDelete('cascade');
            //$table->foreignId('institution_group_id')->constrained('institution_groups')->onDelete('cascade');
            $table->foreignUuid('mpp_id')->constrained()->onDelete('cascade');
            //$table->foreignId('mpp_id')->constrained('mpps')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
