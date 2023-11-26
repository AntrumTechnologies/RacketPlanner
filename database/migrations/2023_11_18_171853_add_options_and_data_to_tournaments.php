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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->boolean('leaderboard')->nullable();
            $table->longText('description')->nullable();
            $table->string('location', 255)->nullable();
            $table->text('location_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('leaderboard');
            $table->dropColumn('description');
            $table->dropColumn('location');
            $table->dropColumn('location_link');
        });
    }
};
