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
        Schema::create('v2_our_programs', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail', 255)->nullable();
            $table->string('judul', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('link');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v2_our_programs');
    }
};
