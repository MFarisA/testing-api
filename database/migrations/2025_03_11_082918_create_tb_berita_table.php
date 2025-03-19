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
        Schema::create('tb_berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('path_media', 1000);
            $table->string('link', 1000)->nullable();
            $table->string('filename', 255)->nullable();
            $table->longText('deskripsi');
            $table->timestamp('waktu')->useCurrent();
            $table->foreignId('id_uploader')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('tb_kategori', 'id_kategori')->onDelete('cascade');
            $table->integer('publish')->default(0);
            $table->integer('open')->default(0);
            $table->string('cover', 1000)->default('noimage.jpg');
            $table->string('keyword', 500);
            $table->tinyInteger('editor')->default(0);
            $table->tinyInteger('library')->default(0);
            $table->tinyInteger('redaktur')->default(0);
            $table->dateTime('waktu_publish')->nullable();
            $table->foreignId('program_id')->nullable()->constrained('tb_program', 'id_program')->onDelete('cascade');
            $table->enum('type', ['video', 'cetak', 'old'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_berita');
    }
};
