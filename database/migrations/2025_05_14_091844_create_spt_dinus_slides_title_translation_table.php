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
        Schema::create('spt_dinus_slides_title_translation', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->integer('urutan')->nullable();
            $table->integer('spt_dinus_title_id'); 

            $table->foreign('spt_dinus_title_id')->references('id')->on('v2_sptdinus_slides_title')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spt_dinus_slides_title_translation');
    }
};
