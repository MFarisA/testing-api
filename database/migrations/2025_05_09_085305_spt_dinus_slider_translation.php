<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt_dinus_slider_translation', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_hover')->nullable();
            $table->string('teks')->nullable();
            $table->text('link')->nullable();
            $table->longText('deskripsi');
            $table->integer('spt_dinus_slider_id'); 

            $table->foreign('spt_dinus_slider_id')->references('id')->on('v2_sptdinus_slider')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt_dinus_slider_translation');
    }
};
