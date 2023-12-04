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
            $table->double('number_of_matches')->nullable();
            $table->double('partner_rating_tolerance')->nullable();
            $table->double('team_rating_tolerance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('number_of_matches');
            $table->dropColumn('partner_rating_tolerance');
            $table->dropColumn('team_rating_tolerance');
        });
    }
};
