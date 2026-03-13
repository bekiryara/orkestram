<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->string('slug', 140)->unique();
                $table->unsignedInteger('sort_order')->default(100);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
                $table->string('name', 140);
                $table->string('slug', 160);
                $table->unsignedInteger('sort_order')->default(100);
                $table->timestamps();

                $table->unique(['city_id', 'slug'], 'districts_city_slug_unique');
                $table->index(['city_id', 'sort_order'], 'districts_city_sort_index');
            });
        }

        if (!Schema::hasTable('neighborhoods')) {
            Schema::create('neighborhoods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
                $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
                $table->string('name', 180);
                $table->string('slug', 200);
                $table->unsignedInteger('sort_order')->default(100);
                $table->timestamps();

                $table->unique(['district_id', 'slug'], 'neighborhoods_district_slug_unique');
                $table->index(['city_id', 'district_id', 'sort_order'], 'neighborhoods_city_district_sort_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('neighborhoods')) {
            Schema::drop('neighborhoods');
        }
        if (Schema::hasTable('districts')) {
            Schema::drop('districts');
        }
        if (Schema::hasTable('cities')) {
            Schema::drop('cities');
        }
    }
};

