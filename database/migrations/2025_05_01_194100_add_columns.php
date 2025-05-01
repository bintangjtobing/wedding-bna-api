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
        Schema::table('contacts', function (Blueprint $table) {
            // Tambahkan kolom baru setelah phone_number
            $table->string('username')->after('name')->nullable();
            $table->string('country', 2)->after('phone_number')->default('ID');
            $table->string('country_code', 5)->after('country')->default('62');
            $table->string('greeting')->after('country_code')->nullable();

            // Tambahkan indeks pada username untuk pencarian yang lebih cepat
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Hapus kolom jika perlu rollback
            $table->dropColumn(['username', 'country', 'country_code', 'greeting']);

            // Hapus indeks
            $table->dropIndex(['username']);
        });
    }
};