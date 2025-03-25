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
        Schema::create('spt_dinus_slider_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_dinus_slider_id')->constrained('v2_home_slider')->onDelete('cascade');
            $table->foreignId('translation_id')->constrained('translations')->onDelete('cascade');
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_hover')->nullable();
            $table->string('teks')->nullable();
            $table->string('link')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spt_dinus_slider_translation');
    }
};
