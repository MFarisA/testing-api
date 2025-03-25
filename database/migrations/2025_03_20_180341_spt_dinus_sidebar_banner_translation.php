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
        Schema::create('spt_dinus_sidebar_banner_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_dinus_sidebar_banner_id')
                ->constrained('v2_sptudinus_sidebar_banner')
                ->onDelete('cascade')
                ->name('fk_sidebar_banner');
                
            $table->foreignId('translation_id')
                ->constrained('translations')
                ->onDelete('cascade')
                ->name('fk_sidebar_translation');
        
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spt_dinus_sidebar_banner_translation');
    }
};
