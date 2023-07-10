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
        Schema::create('match_matrix', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tournament');
            $table->integer('round');
            $table->bigInteger('court');
            $table->bigInteger('match');
            $table->boolean('available');
            $table->boolean('public');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_matrix');
    }
};
