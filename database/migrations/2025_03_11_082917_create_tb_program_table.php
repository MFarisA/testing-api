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
        Schema::create('tb_program', function (Blueprint $table) {
            $table->id('id_program');
            $table->string('judul', 255);
            $table->string('video', 255)->nullable();
            $table->string('thumbnail', 1000)->nullable();
            $table->longText('deskripsi')->nullable();
            $table->longText('deskripsi_pendek')->nullable();
            $table->unsignedBigInteger('id_acara');
            $table->foreign('id_acara')->references('id_acara')->on('tb_acara')->onDelete('cascade');
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_program');
    }
};
