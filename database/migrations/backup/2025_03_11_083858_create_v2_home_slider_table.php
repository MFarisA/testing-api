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
        Schema::create('v2_home_slider', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255)->nullable();
            $table->string('sub_judul', 255)->nullable();
            $table->string('gambar', 255)->nullable();
            $table->integer('urutan')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v2_home_slider');
    }
};
