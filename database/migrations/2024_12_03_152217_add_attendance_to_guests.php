<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('guests', function (Blueprint $table) {
            // Menambahkan kolom baru 'attendance'
            $table->string('attendance_name')->nullable(); // Nama
            $table->text('attendance_message')->nullable(); // Pesan
            $table->boolean('attend')->default(0); // Attend: 1 untuk hadir, 0 untuk tidak hadir
        });
    }

    public function down()
    {
        Schema::table('guests', function (Blueprint $table) {
            // Menghapus kolom 'attendance' jika rollback migrasi
            $table->dropColumn('attendance_name');
            $table->dropColumn('attendance_message');
            $table->dropColumn('attend');
        });
    }
};