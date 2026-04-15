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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('olsera_id')->unique();
            $table->string('sku')->index()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('buy_price', 12, 2)->default(0);
            $table->decimal('weight', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->json('images')->nullable();
            $table->boolean('is_variant')->default(false);
            $table->boolean('allow_decimal')->default(false);
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
