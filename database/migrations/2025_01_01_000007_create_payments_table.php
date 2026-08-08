<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('reference')->unique();
            $table->string('gateway')->default('paystack');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('channel')->nullable();     // card, bank, ussd, etc — from Paystack response
            $table->enum('status', ['pending', 'success', 'failed', 'abandoned'])->default('pending');
            $table->json('gateway_response')->nullable(); // full verified payload, kept for audit/dispute resolution
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
