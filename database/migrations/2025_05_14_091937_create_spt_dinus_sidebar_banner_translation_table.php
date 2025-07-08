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
        Schema::create('spt_dinus_sidebar_banner_translation', function (Blueprint $table) {
            $table->id();
            $table->string('gambar')->nullable();
            $table->integer('spt_dinus_banner_id'); 

            $table->foreign('spt_dinus_banner_id')->references('id')->on('v2_sptudinus_sidebar_banner')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spt_dinus_sidebar_banner_translation');
    }
};
