<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recenttrailer_translation', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->date('date')->nullable();
            $table->string('youtube_id');
            $table->integer('recenttrailer_id'); 

            $table->foreign('recenttrailer_id')->references('id')->on('v2_recenttrailer')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recenttrailer_translation');
    }
};
