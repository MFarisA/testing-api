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
        Schema::create('home_whoweare_translation', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->longText('deskripsi');
            $table->string('gambar')->nullable();
            $table->string('motto1')->nullable();
            $table->string('motto2')->nullable();
            $table->string('motto3')->nullable();
            $table->text('motto1sub')->nullable();
            $table->text('motto2sub')->nullable();
            $table->text('motto3sub')->nullable();
            $table->integer('whoweare_id'); 

            $table->foreign('whoweare_id')->references('id')->on('v2_home_whoweare')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_whoweare_translation');
    }
};
