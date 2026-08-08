<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // "Daily", "Weekly 2 Devices", etc.
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->string('duration_label');            // human label: "24 Hours", "7 Days"
            $table->unsignedInteger('duration_minutes'); // machine-usable, for future expiry/MikroTik logic
            $table->unsignedTinyInteger('device_limit')->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
