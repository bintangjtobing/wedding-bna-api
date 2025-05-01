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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained()->onDelete('cascade'); // Admin yang mengirim
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('response')->nullable(); // Response dari API WhatsApp
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_logs');
    }
};
