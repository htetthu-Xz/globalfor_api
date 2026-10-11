<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_asset_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('position');
            $table->string('alt_text', 500);
            $table->timestamps();

            $table->unique(['activity_id', 'position']);
            $table->unique(['activity_id', 'media_asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_images');
    }
};
