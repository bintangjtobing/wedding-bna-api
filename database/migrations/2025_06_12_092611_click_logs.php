<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('click_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->string('username');
            $table->string('name');
            $table->string('ip_address');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('continent')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('zipcode')->nullable();
            $table->string('country_emoji')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_brand')->nullable();
            $table->string('os_name')->nullable();
            $table->string('browser_name')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['contact_id', 'clicked_at']);
            $table->index('username');
            $table->index('ip_address');
            $table->index('clicked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_logs');
    }
};