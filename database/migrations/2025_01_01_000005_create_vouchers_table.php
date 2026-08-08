<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('username');
            $table->string('password');
            $table->enum('status', ['unused', 'reserved', 'sold', 'expired'])->default('unused');

            // Filled in only once sold — nullable by design so unused stock has no customer/order attached.
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->index();

            $table->timestamp('imported_at')->useCurrent();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Scoped per site: the same username could coincidentally appear at two
            // different physical locations without actually being a duplicate.
            $table->unique(['site_id', 'package_id', 'username']);
            $table->index(['site_id', 'package_id', 'status']); // the exact lookup used when assigning a voucher
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
