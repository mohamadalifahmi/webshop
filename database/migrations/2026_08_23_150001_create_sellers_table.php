<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name', 150);
            $table->string('slug', 160)->unique();
            $table->text('description')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('governorate', 60)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->decimal('commission_override', 5, 2)->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('rejection_reason', 500)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->index('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
