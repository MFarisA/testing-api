<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_ourprograms_translation', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->text('link');
            $table->integer('urutan')->nullable();
            $table->integer('ourprogram_id'); 

            $table->foreign('ourprogram_id')->references('id')->on('v2_our_programs')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_ourprograms_translation');
    }
};
