<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('listings', 'avenue_name')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->string('avenue_name', 180)->nullable()->after('district');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('listings', 'avenue_name')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('avenue_name');
            });
        }
    }
};
