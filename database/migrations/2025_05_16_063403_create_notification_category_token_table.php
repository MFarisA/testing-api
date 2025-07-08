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
        Schema::create('notification_category_token', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_token_id');
            $table->unsignedBigInteger('notification_category_id');
            $table->timestamps();
            // $table->unique(['notification_token_id', 'notification_category_id']);
            $table->unique(
                ['notification_token_id', 'notification_category_id'],
                'notif_category_token_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_category_token');
    }
};
