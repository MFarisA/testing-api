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
        Schema::create('marketing_translations', function (Blueprint $table) {
            $table->id();
            $table->text('judul');
            $table->text('foto')->nullable();
            $table->text('isi')->nullable();
            $table->integer('marketing_id'); 

            $table->foreign('marketing_id')->references('id')->on('tb_marketing')->onDelete('cascade');
            $table->foreignId('translation_id')->references('id')->on('translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_translations');
    }
};
