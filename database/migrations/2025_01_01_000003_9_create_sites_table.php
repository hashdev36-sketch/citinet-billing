<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per physical hotspot location (Citinet 1, Citinet 2, ...).
        // Adding a 5th location later is a row here, not a schema change or deploy.
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // "Citinet 1"
            $table->string('slug')->unique();  // "citinet1" — matches the siteId convention already used in the Next.js backend
            $table->string('address')->nullable();
            $table->string('whatsapp_number')->nullable();   // per-site support contact, falls back to global setting if null
            $table->string('telegram_admin_chat_id')->nullable(); // mirrors the site registry pattern from the existing Telegram bot backend
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
