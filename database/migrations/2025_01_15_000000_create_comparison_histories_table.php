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
        Schema::create('comparison_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop1_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('shop2_id')->constrained('shops')->cascadeOnDelete();
            $table->string('keyword');
            $table->string('sort')->default('23');
            $table->unsignedInteger('shop1_product_count')->default(0);
            $table->unsignedInteger('shop2_product_count')->default(0);
            $table->unsignedBigInteger('shop1_min_price')->nullable();
            $table->unsignedBigInteger('shop2_min_price')->nullable();
            $table->timestamps();

            $table->index('keyword');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparison_histories');
    }
};
