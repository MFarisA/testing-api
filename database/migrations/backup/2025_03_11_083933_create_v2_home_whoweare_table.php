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
        Schema::create('v2_home_whoweare', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255)->nullable();
            $table->longText('deskripsi')->nullable();
            $table->string('gambar', 255)->nullable();
            $table->string('motto1', 100)->nullable();
            $table->string('motto2', 100)->nullable();
            $table->string('motto3', 100)->nullable();
            $table->text('motto1sub');
            $table->text('motto2sub');
            $table->text('motto3sub');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v2_home_whoweare');
    }
};
