<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Simple key/value store editable from Admin > Settings.
        // .env values (see .env.example) act as fallback defaults on first boot.
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('actor');   // Admin or User who performed the action
            $table->string('action');          // e.g. "voucher.csv_import", "order.refunded"
            $table->nullableMorphs('subject');  // the model affected, e.g. Order#123
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
    }
};
