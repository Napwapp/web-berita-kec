<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Notifiable polymorphic relationship (bisa untuk User, atau model lain jika diperlukan)
            $table->id();
            $table->morphs('notifiable');
            // Pengirim notifikasi, nullable
            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('news_id')
                ->nullable()
                ->constrained('news')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('type');
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifiable_read_idx');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
