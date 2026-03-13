<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('listing_likes')) {
            Schema::create('listing_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('site', 64)->default('orkestram.net');
                $table->string('actor_key', 190);
                $table->timestamps();

                $table->unique(['listing_id', 'actor_key']);
                $table->index(['site', 'created_at']);
            });
        }

        if (!Schema::hasTable('listing_feedback')) {
            Schema::create('listing_feedback', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('answered_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('site', 64)->default('orkestram.net');
                $table->string('kind', 32)->default('comment');
                $table->string('visibility', 16)->default('public');
                $table->string('status', 32)->default('pending');
                $table->text('content');
                $table->text('owner_reply')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->timestamps();

                $table->index(['site', 'kind', 'status']);
                $table->index(['listing_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_feedback');
        Schema::dropIfExists('listing_likes');
    }
};
