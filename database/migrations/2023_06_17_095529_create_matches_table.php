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
            $table->id()->unique();
            $table->bigInteger('tournament_id')->unsigned();
            $table->bigInteger('player1a_id')->unsigned()->nullable()->index();
            $table->bigInteger('player1b_id')->unsigned()->nullable()->index();
            $table->bigInteger('player2a_id')->unsigned()->nullable()->index();
            $table->bigInteger('player2b_id')->unsigned()->nullable()->index();
            $table->double('rating')->nullable();
            $table->double('rating_diff')->nullable();
            $table->boolean('disabled')->default(false); // For internal usage of scheduler
            $table->double('priority')->default(0);
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
