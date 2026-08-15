
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('return_number')->unique();

            $table->string('status')->default('pending')->index();

            $table->string('reason')->nullable();

            $table->text('customer_note')->nullable();

            $table->text('admin_note')->nullable();

            $table->timestamp('requested_at')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamp('rejected_at')->nullable();

            $table->timestamp('received_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};

