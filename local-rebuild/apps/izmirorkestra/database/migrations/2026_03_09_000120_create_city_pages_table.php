<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_pages', function (Blueprint $table) {
            $table->id();
            $table->string('site', 64)->default('orkestram.net');
            $table->string('slug', 255);
            $table->string('city', 120);
            $table->string('district', 120)->nullable();
            $table->string('service_slug', 120)->nullable();
            $table->string('title', 255);
            $table->string('status', 20)->default('draft');
            $table->longText('content')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['site', 'slug']);
            $table->index(['site', 'city', 'district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_pages');
    }
};
