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
        Schema::table('users', function (Blueprint $table) {    
            $table->double('rating', 2, 2)->nullable();
            $table->string('avatar')->nullable();
            $table->string('fcm_token')->nullable();
            $table->dateTime('availability_start')->nullable();
            $table->dateTime('availability_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {    
            $table->dropColumn('rating');
            $table->dropColumn('avatar');
            $table->dropColumn('availability_start');
            $table->dropColumn('availability_end');
        });
    }
};
