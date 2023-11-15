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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->dateTime('datetime_start')->nullable();
            $table->dateTime('datetime_end')->nullable();
            $table->integer('matches')->nullable(); // Number of matches
            $table->integer('duration_m')->nullable(); // Duration in minutes
            $table->enum('type', ['single', 'double', 'mix'])->nullable();
            $table->boolean('allow_singles')->nullable();
            $table->integer('time_between_matches_m')->nullable(); // In minutes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
