
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_id')
                ->constrained('returns')
                ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->string('reason')->nullable();

            $table->string('condition')->nullable();

            $table->timestamps();

            $table->index(['return_id', 'order_item_id']);

            $table->unique([
                'return_id',
                'order_item_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
