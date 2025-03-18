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
        Schema::create('v2_sptdinus_slider', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_slides_title');
            $table->string('thumbnail', 255)->nullable();
            $table->string('thumbnail_hover', 255)->nullable();
            $table->string('teks', 255)->nullable();
            $table->text('link')->nullable();
            $table->longText('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v2_sptdinus_slider');
    }
};
