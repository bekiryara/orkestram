<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('site', 64)->default('orkestram.net');
            $table->string('slug', 255);
            $table->string('title', 255);
            $table->string('template', 64)->default('page');
            $table->string('status', 20)->default('draft');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->string('canonical_url', 512)->nullable();
            $table->json('schema_json')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['site', 'slug']);
            $table->index(['site', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
