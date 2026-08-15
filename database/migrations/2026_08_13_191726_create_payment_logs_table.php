<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('action');

            $table->string('status')->nullable();

            $table->decimal('amount', 12, 2)->nullable();

            $table->string('transaction_id')->nullable();

            $table->text('message')->nullable();

            $table->json('payload')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['payment_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};