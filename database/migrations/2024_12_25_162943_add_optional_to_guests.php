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
        Schema::table('guests', function (Blueprint $table) {
            $table->enum('specific_call', ['mr', 'bang', 'kak', 'mas', 'mrs', 'ms'])->nullable()->after('comment');
            $table->string('region')->nullable()->after('specific_call');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('region');
            $table->enum('friend_of', ['Bintang', 'Ayu'])->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['specific_call', 'region', 'gender', 'friend_of']);
        });
    }
};