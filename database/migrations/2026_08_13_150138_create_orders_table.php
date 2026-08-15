<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('order_number', 30)->unique();

            // حالة الطلب
            $table->string('status', 30)->default('pending');

            // حالة الدفع
            $table->string('payment_status', 30)->default('pending');

            // طريقة الدفع
            $table->string('payment_method', 30)->default('cod');

            // المبالغ
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // بيانات الشحن - Snapshot
            $table->string('customer_name');
            $table->string('customer_phone', 30);

            $table->string('shipping_address');
            $table->string('shipping_city')->nullable();
            $table->string('shipping_area')->nullable();
            $table->string('shipping_postal_code', 20)->nullable();

            // ملاحظات العميل
            $table->text('customer_note')->nullable();

            // بيانات إضافية
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};