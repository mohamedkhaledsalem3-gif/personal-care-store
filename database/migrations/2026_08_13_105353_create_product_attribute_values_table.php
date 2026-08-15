<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_attribute_id')
                ->constrained('product_attributes')
                ->cascadeOnDelete();

            $table->string('value');
            $table->string('slug');

            $table->string('color_code')->nullable();

            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique([
                'product_attribute_id',
                'slug',
            ]);

            $table->index([
                'product_attribute_id',
                'is_active',
                'sort_order',
            ], 'pav_attr_active_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};