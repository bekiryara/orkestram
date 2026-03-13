<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('site', 64)->default('orkestram.net');
            $table->string('old_path', 512);
            $table->string('new_url', 1024);
            $table->unsignedSmallInteger('http_code')->default(301);
            $table->boolean('is_active')->default(false);
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique(['site', 'old_path']);
            $table->index(['site', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
