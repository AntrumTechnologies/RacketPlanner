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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('player1');
            $table->bigInteger('player2');
            $table->bigInteger('player3')->nullable();
            $table->bigInteger('player4')->nullable();
            $table->bigInteger('court');
            $table->dateTime('datetime', 0);
            $table->integer('score1-2')->nullable();
            $table->integer('score3-4')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
