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
        Schema::create('spt_dinus_slides_title_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_dinus_slides_title_id')
                ->constrained('v2_sptdinus_slides_title')
                ->onDelete('cascade')
                ->name('fk_dinus_title');
            $table->foreignId('translation_id')
                ->constrained('translations')
                ->onDelete('cascade')
                ->name('fk_translation');
            $table->string('judul');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spt_dinus_slides_title_translation');
    }
};
