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
        Schema::create('acara_translation', function (Blueprint $table) {
            $table->id();
            $table->string('nama_acara');
            $table->string('thumbnail_acara')->nullable();
            $table->text('description')->nullable();
            $table->integer('acara_id'); 
        
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acara_translation');
    }
};
