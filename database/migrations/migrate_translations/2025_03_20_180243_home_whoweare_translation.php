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
            $table->foreignId('whoweare_id')->constrained('v2_home_whoweare')->onDelete('cascade');
            $table->foreignId('translation_id')->constrained('translations')->onDelete('cascade');
            $table->string('judul');
            $table->longText('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('motto1')->nullable();
            $table->string('motto2')->nullable();
            $table->string('motto3')->nullable();
            $table->string('motto1sub')->nullable();
            $table->string('motto2sub')->nullable();
            $table->string('motto3sub')->nullable();
            $table->timestamps();
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
