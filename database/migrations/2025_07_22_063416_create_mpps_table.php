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
        Schema::create('mpps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique()->nullable();
            $table->string('name'); // Contoh: MPP Kota Magelang
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpps');
    }
};
