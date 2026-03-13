<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('cover_image_path', 255)->nullable()->after('price_label');
            $table->json('gallery_json')->nullable()->after('cover_image_path');
            $table->string('whatsapp', 32)->nullable()->after('gallery_json');
            $table->string('phone', 32)->nullable()->after('whatsapp');
            $table->json('features_json')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image_path',
                'gallery_json',
                'whatsapp',
                'phone',
                'features_json',
            ]);
        });
    }
};
