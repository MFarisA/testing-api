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
        Schema::create('home_slider_translation', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->string('sub_judul')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('urutan')->nullable();
            $table->text('url')->nullable();
            $table->integer('slider_id'); 

            $table->foreign('slider_id')->references('id')->on('v2_home_slider')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_slider_translation');
    }
};
