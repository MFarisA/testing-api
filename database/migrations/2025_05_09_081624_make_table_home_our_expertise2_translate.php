<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_ourexpertise2_translation', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail')->nullable();
            $table->string('judul');
            $table->integer('ourexpertise2_id'); 

            $table->foreign('ourexpertise2_id')->references('id')->on('v2_home_ourexpertise2')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_ourexpertise2_translation');
    }
};
