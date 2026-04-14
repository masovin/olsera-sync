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
        Schema::create('sync_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('olsera_id')->index();
            $table->string('woocommerce_id')->index();
            $table->unique(['olsera_id', 'woocommerce_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_mappings');
    }
};
