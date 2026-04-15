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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->index()->after('description');
            }
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'buy_price')) {
                $table->decimal('buy_price', 12, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 10, 2)->default(0)->after('buy_price');
            }
            if (!Schema::hasColumn('products', 'has_variants')) {
                if (Schema::hasColumn('products', 'is_variant')) {
                    $table->renameColumn('is_variant', 'has_variants');
                } else {
                    $table->boolean('has_variants')->default(false)->after('images');
                }
            }
            if (!Schema::hasColumn('products', 'allow_decimal')) {
                $table->boolean('allow_decimal')->default(false)->after('has_variants');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'buy_price', 'weight', 'has_variants', 'allow_decimal']);
        });
    }
};
