<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_attributes', function (Blueprint $table): void {
            if (!Schema::hasColumn('category_attributes', 'filter_mode')) {
                $table->string('filter_mode', 24)->default('exact')->after('field_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('category_attributes', function (Blueprint $table): void {
            if (Schema::hasColumn('category_attributes', 'filter_mode')) {
                $table->dropColumn('filter_mode');
            }
        });
    }
};

