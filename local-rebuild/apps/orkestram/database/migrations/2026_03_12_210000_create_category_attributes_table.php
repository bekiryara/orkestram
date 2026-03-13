<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_attributes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('label', 120);
            $table->string('field_type', 24);
            $table->json('options_json')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_visible_in_card')->default(false);
            $table->boolean('is_visible_in_detail')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(100);
            $table->timestamps();

            $table->unique(['category_id', 'key'], 'category_attributes_category_key_unique');
            $table->index(['category_id', 'is_active', 'sort_order'], 'category_attributes_category_active_sort_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_attributes');
    }
};

