<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puzzle_slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_slug', 180)->unique();
            $table->foreignId('puzzle_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puzzle_slug_redirects');
    }
};
