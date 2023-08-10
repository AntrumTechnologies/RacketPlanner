<?php

namespace Database\Seeders;

use App\Models\Round;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoundsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rounds')->truncate();

        Round::create(['name' => 'Clinic', 'tournament_id' => 1, 'starttime' => '2023-08-19 10:15', 'endtime' => '2023-08-19 11:15']);
        Round::create(['name' => 'Round 1', 'tournament_id' => 1, 'starttime' => '2023-08-19 10:30', 'endtime' => '2023-08-19 11:15']);
        Round::create(['name' => 'Round 2', 'tournament_id' => 1, 'starttime' => '2023-08-19 11:15', 'endtime' => '2023-08-19 12:00']);
        Round::create(['name' => 'Round 3', 'tournament_id' => 1, 'starttime' => '2023-08-19 12:45', 'endtime' => '2023-08-19 13:30']);
        Round::create(['name' => 'Round 4', 'tournament_id' => 1, 'starttime' => '2023-08-19 13:30', 'endtime' => '2023-08-19 14:15']);
        Round::create(['name' => 'Round 5', 'tournament_id' => 1, 'starttime' => '2023-08-19 14:15', 'endtime' => '2023-08-19 15:00']);
        Round::create(['name' => 'Round 6', 'tournament_id' => 1, 'starttime' => '2023-08-19 15:00', 'endtime' => '2023-08-19 15:40']);

        Round::create(['name' => 'Clinic', 'tournament_id' => 2, 'starttime' => '2023-08-19 10:15', 'endtime' => '2023-08-19 11:15']);
        Round::create(['name' => 'Round 1', 'tournament_id' => 2, 'starttime' => '2023-08-19 10:30', 'endtime' => '2023-08-19 11:15']);
        Round::create(['name' => 'Round 2', 'tournament_id' => 2, 'starttime' => '2023-08-19 11:15', 'endtime' => '2023-08-19 12:00']);
        Round::create(['name' => 'Round 3', 'tournament_id' => 2, 'starttime' => '2023-08-19 12:45', 'endtime' => '2023-08-19 13:30']);
        Round::create(['name' => 'Round 4', 'tournament_id' => 2, 'starttime' => '2023-08-19 13:30', 'endtime' => '2023-08-19 14:15']);
        Round::create(['name' => 'Round 5', 'tournament_id' => 2, 'starttime' => '2023-08-19 14:15', 'endtime' => '2023-08-19 13:00']);
        Round::create(['name' => 'Round 5', 'tournament_id' => 2, 'starttime' => '2023-08-19 14:15', 'endtime' => '2023-08-19 15:00']);
        Round::create(['name' => 'Round 6', 'tournament_id' => 2, 'starttime' => '2023-08-19 15:00', 'endtime' => '2023-08-19 15:45']);
    }
}
