<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puzzles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('cover_image_path')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('full_title', 200)->nullable();
            $table->longText('description')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('audio_path')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('coordinates', 120)->nullable();
            $table->string('maps_url', 500)->nullable();
            $table->longText('benefits')->nullable();
            $table->string('extra_image_path')->nullable();
            $table->string('cta_text', 200)->nullable();
            $table->string('cta_link', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puzzles');
    }
};
