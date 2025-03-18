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
        Schema::create('tb_acara', function (Blueprint $table) {
            $table->id('id_acara');
            $table->string('nama_acara', 250)->nullable();
            $table->string('thumbnail_acara', 1000)->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('path', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_acara');
    }
};
