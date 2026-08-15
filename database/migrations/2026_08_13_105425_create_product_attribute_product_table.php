<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attribute_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_value_id')
                ->constrained('product_attribute_values')
                ->cascadeOnDelete();

            $table->timestamps();

         $table->unique(
    ['product_id', 'product_attribute_value_id'],
    'pap_product_value_unique'
);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_product');
    }
};