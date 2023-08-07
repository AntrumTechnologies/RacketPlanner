<?php

namespace Database\Seeders;

use App\Models\Tournament;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TournamentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tournaments')->truncate();

        Tournament::create([
            "name" => "Prodrive Tennis Tournament 2023",
            "datetime_start" => "2023-08-19 10:15",
            "datetime_end" => "2023-08-19 16:00",
            "created_by" => 1,
        ]);

        Tournament::create([
            "name" => "Prodrive Padel Tournament 2023",
            "datetime_start" => "2023-08-19 10:15",
            "datetime_end" => "2023-08-19 16:00",
            "created_by" => 1,
        ]);
    }
}
