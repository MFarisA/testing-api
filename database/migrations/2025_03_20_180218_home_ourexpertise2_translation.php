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
        Schema::create('home_ourexpertise2_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ourexpertise2_id')
                  ->constrained('v2_home_ourexpertise2')
                  ->onDelete('cascade');
            $table->foreignId('translation_id')
                  ->constrained('translations')
                  ->onDelete('cascade');
            $table->string('thumbnail')->nullable();
            $table->string('judul');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_ourexpertise2_translation');
    }
};
