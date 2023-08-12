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
            $table->double('rating')->nullable();
            $table->string('avatar')->nullable()->default('avatars/placeholder.png');
            $table->string('fcm_token')->nullable();
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
            $table->dropColumn('fcm_token');
        });
    }
};
