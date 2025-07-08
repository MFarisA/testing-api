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
        Schema::create('kategori_translations', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('slug', 20);

            $table->integer('id_kategori');
            $table->foreign('id_kategori')->references('id_kategori')->on('tb_kategori')->onDelete('cascade');
            $table->foreignId('translation_id')->constrained('translations')->onDelete('cascade');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
