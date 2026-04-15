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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('olsera_id')->unique();
            $table->string('sku')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('sell_price', 12, 2)->nullable();
            $table->decimal('buy_price', 12, 2)->default(0);
            $table->decimal('weight', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('barcode')->nullable()->index();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
