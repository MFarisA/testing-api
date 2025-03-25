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
            $table->foreignId('acara_id')->constrained('tb_acara', 'id_acara')->onDelete('cascade');
            $table->foreignId('translation_id')->constrained('translations')->onDelete('cascade');
            $table->string('nama_acara');
            $table->string('thumbnail_acara')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
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
