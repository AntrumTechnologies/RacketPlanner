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
        Schema::table('organizations', function (Blueprint $table) {
		$table->string('contact_person')->nullable()->change();
		$table->string('contact_email')->nullable()->change();
	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
