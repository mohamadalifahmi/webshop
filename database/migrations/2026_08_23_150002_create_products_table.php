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
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 180);
            $table->string('slug', 200)->unique();
            $table->string('sku', 64)->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->enum('status', ['draft', 'pending', 'active', 'rejected'])->default('draft');
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->string('rejection_reason', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->index('status');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
