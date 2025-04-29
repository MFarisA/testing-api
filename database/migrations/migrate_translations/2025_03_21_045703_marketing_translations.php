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
        Schema::create('marketing_translation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketing_id');
            $table->string('locale')->index();
            $table->string('judul')->nullable();
            $table->text('isi')->nullable();
            $table->timestamps();
        
            $table->foreign('marketing_id')->references('id')->on('tb_marketing')->onDelete('cascade');
            $table->unique(['marketing_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_translation');
    }
};
