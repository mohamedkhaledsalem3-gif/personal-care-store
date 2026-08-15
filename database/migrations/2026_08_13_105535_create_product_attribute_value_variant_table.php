<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attribute_value_variant', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            /*
             * اسم الـ Foreign Key الافتراضي اللي كان Laravel هيولّده
             * (product_attribute_value_variant_product_attribute_value_id_foreign)
             * أطول من 64 حرف اللي MySQL بيسمح بيهم، فبنمرر اسم مختصر
             * صراحة عبر الباراميتر indexName بدل الاعتماد على التوليد التلقائي.
             */
            $table->foreignId('product_attribute_value_id')
                ->constrained(
                    table: 'product_attribute_values',
                    indexName: 'pav_variant_fk'
                )
                ->cascadeOnDelete();

            $table->timestamps();

            /*
             * نفس المشكلة: الاسم الافتراضي لهذا الـ unique كان
             * هيبقى 84 حرف. بنحدد اسم مختصر يدويًا.
             */
            $table->unique(
                ['product_variant_id', 'product_attribute_value_id'],
                'pav_variant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value_variant');
    }
};
