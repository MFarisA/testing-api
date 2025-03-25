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
        Schema::create('recenttrailer_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recenttrailer_id')->constrained('v2_recenttrailer')->onDelete('cascade');
            $table->foreignId('translation_id')->constrained('translations')->onDelete('cascade');
            $table->string('judul');
            $table->string('youtube_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recenttrailer_translation');
    }
};
