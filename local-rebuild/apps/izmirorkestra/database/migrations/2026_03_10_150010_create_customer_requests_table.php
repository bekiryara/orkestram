<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_requests')) {
            Schema::create('customer_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('listing_id')->nullable()->constrained('listings')->nullOnDelete();
                $table->string('site', 64)->default('orkestram.net');
                $table->string('name', 255);
                $table->string('phone', 64)->nullable();
                $table->string('email', 255)->nullable();
                $table->text('message')->nullable();
                $table->string('status', 32)->default('new');
                $table->timestamps();

                $table->index(['site', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_requests');
    }
};
