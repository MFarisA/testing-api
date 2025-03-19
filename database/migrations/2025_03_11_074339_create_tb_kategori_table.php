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
        Schema::create('tb_kategori', function (Blueprint $table) {
            $table->id('id_kategori'); // Primary key
            $table->string('nama', 255)->unique(); // Nama kategori, unik
            $table->string('slug', 255)->unique(); // Slug, panjang 255 karakter
            $table->boolean('top_nav')->default(false); // Gunakan boolean untuk true/false
            $table->integer('urutan')->default(0); // Urutan kategori
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
