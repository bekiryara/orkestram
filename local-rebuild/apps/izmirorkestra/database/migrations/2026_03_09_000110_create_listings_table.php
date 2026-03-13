<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('site', 64)->default('orkestram.net');
            $table->string('slug', 255);
            $table->string('name', 255);
            $table->string('status', 20)->default('draft');
            $table->string('city', 120)->nullable();
            $table->string('district', 120)->nullable();
            $table->string('service_type', 120)->nullable();
            $table->string('price_label', 120)->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['site', 'slug']);
            $table->index(['site', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
