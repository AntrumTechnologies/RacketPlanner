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
            $table->id()->unique();
            $table->bigInteger('tournament_id')->unsigned();
            $table->bigInteger('round_id')->unsigned()->index();
            $table->bigInteger('court_id')->unsigned()->index();
            $table->enum('state', ['available', 'disabled', 'clinic'])->default('available');
            $table->bigInteger('match_id')->nullable();
            $table->boolean('public')->default(false);
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
