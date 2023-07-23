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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tournament_id');
            $table->bigInteger('court_id');
            $table->bigInteger('player1a');
            $table->bigInteger('player1b')->nullable();
            $table->bigInteger('player2a');
            $table->bigInteger('player2b')->nullable();
            $table->datetime('datetime', 0);
            $table->integer('score1')->nullable();
            $table->integer('score2')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
