<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories')) {
            return;
        }

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_category_id')->constrained('main_categories')->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('slug', 160)->unique();
            $table->string('short_description', 320)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_path', 255)->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_indexable')->default(true);
            $table->unsignedInteger('sort_order')->default(100);
            $table->timestamps();

            $table->index(['main_category_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('categories')) {
            Schema::drop('categories');
        }
    }
};
