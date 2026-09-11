<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title', 150)->default('Susurranes');
            $table->string('run_button_text', 80)->default('Run');
            $table->string('run_button_url', 500)->nullable();
            $table->string('android_button_text', 80)->default('Instalar Android');
            $table->string('android_apk_url', 500)->nullable();
            $table->string('about_title', 150)->default('Quiénes somos');
            $table->longText('about_text')->nullable();
            $table->string('about_image_path')->nullable();
            $table->string('about_image_alt', 200)->nullable();
            $table->string('contact_title', 150)->default('Contáctanos');
            $table->string('contact_email', 150)->nullable();
            $table->string('contact_phone', 80)->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('twitter_url', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->string('address', 255)->nullable();
            $table->longText('contact_extra')->nullable();
            $table->json('extra_socials')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
