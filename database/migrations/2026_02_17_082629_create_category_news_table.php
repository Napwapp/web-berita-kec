<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Tabel pivot untuk relasi many-to-many antara news dan categories
    public function up(): void
    {
        Schema::create('category_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['news_id', 'category_id']);

            // speed up query
            $table->index('news_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_news');
    }
};
