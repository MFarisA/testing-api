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
            $table->foreignId('berita_id')
                ->constrained('tb_berita')
                ->onDelete('cascade');
        
            $table->foreignId('translation_id')
                ->constrained('translations')
                ->onDelete('cascade');
        
            $table->string('judul');
            $table->string('links')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->string('cover')->nullable();
            $table->string('keyword')->nullable();
            $table->timestamps();
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
