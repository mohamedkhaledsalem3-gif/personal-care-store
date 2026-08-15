<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();

            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Product Information
            |--------------------------------------------------------------------------
            */

            $table->string('unit')->nullable();

            $table->decimal('weight', 10, 3)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            $table->decimal('cost_price', 12, 2)->default(0);

            $table->decimal('price', 12, 2);

            $table->decimal('sale_price', 12, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('stock_quantity')->default(0);

            $table->unsignedInteger('low_stock_threshold')->default(5);

            /*
            |--------------------------------------------------------------------------
            | Product Status
            |--------------------------------------------------------------------------
            */

            $table->string('status')->default('active');

            $table->boolean('is_featured')->default(false);

            $table->boolean('is_new')->default(true);

            $table->boolean('is_best_seller')->default(false);

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->string('meta_title')->nullable();

            $table->text('meta_description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['category_id', 'status']);

            $table->index(['brand_id', 'status']);

            $table->index('status');

            $table->index('is_featured');

            $table->index('is_new');

            $table->index('is_best_seller');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};