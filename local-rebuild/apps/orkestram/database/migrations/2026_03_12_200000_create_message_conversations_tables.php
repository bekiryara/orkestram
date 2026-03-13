<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('message_conversations')) {
            Schema::create('message_conversations', function (Blueprint $table): void {
                $table->id();
                $table->string('site', 120)->index();
                $table->foreignId('listing_id')->nullable()->constrained('listings')->nullOnDelete();
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 32)->default('active')->index();
                $table->timestamp('last_message_at')->nullable()->index();
                $table->string('last_message_preview', 255)->nullable();
                $table->timestamps();
                $table->unique(['site', 'listing_id', 'owner_user_id', 'customer_user_id'], 'msg_conv_unique');
            });
        }

        if (!Schema::hasTable('message_conversation_messages')) {
            Schema::create('message_conversation_messages', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('conversation_id')->constrained('message_conversations')->cascadeOnDelete();
                $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('sender_role', 32)->default('customer')->index();
                $table->text('body');
                $table->timestamps();
                $table->index(['conversation_id', 'created_at'], 'msg_conv_msg_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('message_conversation_messages');
        Schema::dropIfExists('message_conversations');
    }
};
