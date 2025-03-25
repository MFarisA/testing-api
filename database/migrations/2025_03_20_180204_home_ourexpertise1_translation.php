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
        Schema::create('home_ourexpertise1_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ourexpertise1_id')
                  ->constrained('v2_home_ourexpertise1')
                  ->onDelete('cascade');
            $table->foreignId('translation_id')
                  ->constrained('translations')
                  ->onDelete('cascade');
            $table->string('thumbnail')->nullable();
            $table->string('judul');
            $table->longText('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_ourexpertise1_translation');
    }
};
