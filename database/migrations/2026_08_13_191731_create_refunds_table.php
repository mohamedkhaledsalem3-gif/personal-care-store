<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('return_id')
                ->nullable()
                ->constrained('returns')
                ->nullOnDelete();

            $table->foreignId('payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('refund_number')->unique();

            $table->string('status')->default('pending');

            $table->decimal('amount', 12, 2);

            $table->string('method')->nullable();

            $table->string('transaction_id')->nullable();

            $table->text('reason')->nullable();

            $table->timestamp('requested_at')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamp('failed_at')->nullable();

            $table->text('failure_reason')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['return_id']);
            $table->index(['payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};