<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_attribute_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('category_attribute_id')->constrained('category_attributes')->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 12, 2)->nullable();
            $table->boolean('value_bool')->nullable();
            $table->json('value_json')->nullable();
            $table->string('normalized_value', 255)->nullable();
            $table->timestamps();

            $table->unique(['listing_id', 'category_attribute_id'], 'listing_attribute_values_listing_attr_unique');
            $table->index(['category_attribute_id'], 'listing_attribute_values_attr_index');
            $table->index(['normalized_value'], 'listing_attribute_values_normalized_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_attribute_values');
    }
};

