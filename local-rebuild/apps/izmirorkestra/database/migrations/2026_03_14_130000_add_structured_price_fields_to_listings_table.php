<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table): void {
            $table->decimal('price_min', 12, 2)->nullable()->after('price_label');
            $table->decimal('price_max', 12, 2)->nullable()->after('price_min');
            $table->string('currency', 3)->nullable()->after('price_max');
            $table->string('price_type', 32)->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table): void {
            $table->dropColumn(['price_min', 'price_max', 'currency', 'price_type']);
        });
    }
};

