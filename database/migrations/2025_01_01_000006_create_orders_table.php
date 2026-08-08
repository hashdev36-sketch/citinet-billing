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
            $table->string('order_number')->unique(); // e.g. CIT-20260806-000123, shown to customer
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('site_id')->constrained()->restrictOnDelete(); // location the customer chose at checkout
            $table->foreignId('package_id')->constrained()->restrictOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('NGN');
            $table->enum('status', ['pending', 'paid', 'fulfilled', 'failed', 'refunded'])->default('pending');

            $table->string('paystack_reference')->unique(); // generated before redirecting to Paystack
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        Schema::dropIfExists('orders');
    }
};
