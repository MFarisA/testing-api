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
        Schema::create('berita_translation', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('path_media', 1000)->nullable();
            $table->string('link', 1000)->nullable();
            $table->longText('deskripsi');
            $table->string('cover', 1000)->nullable();
            $table->string('keyword', 500);
            $table->integer('berita_id'); 

            $table->foreign('berita_id')->references('id')->on('tb_berita')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_translation');
    }
};
