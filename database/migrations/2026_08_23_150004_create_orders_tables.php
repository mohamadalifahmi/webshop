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
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('order_number', 24)->unique();
            $table->enum('status', ['pending_payment', 'paid', 'partially_shipped', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'])
                ->default('pending_payment');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', ['stripe', 'manual'])->default('manual');
            $table->enum('payment_status', ['unpaid', 'awaiting_proof', 'under_review', 'paid'])->default('unpaid');
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('ship_to_name', 120);
            $table->string('ship_to_phone', 30);
            $table->string('governorate', 60);
            $table->text('address');
            $table->string('customer_note', 500)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->index('payment_status');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name', 180);
            $table->string('product_sku', 64);
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('seller_earning', 10, 2);
            $table->enum('shipment_status', ['awaiting', 'shipped', 'delivered', 'cancelled'])->default('awaiting');
            $table->string('tracking_number', 80)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancel_deadline_at')->nullable();
            $table->timestamp('earnings_available_at')->nullable();
            $table->boolean('earnings_released')->default(false);
            $table->index('shipment_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
