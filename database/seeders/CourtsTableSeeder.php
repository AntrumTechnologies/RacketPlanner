<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourtsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courts')->truncate();

        Court::create(['name' => 'Court 1', 'tournament_id' => 1, 'created_by' => 1]);
        Court::create(['name' => 'Court 2', 'tournament_id' => 1, 'created_by' => 1]);
        Court::create(['name' => 'Court 3', 'tournament_id' => 1, 'created_by' => 1]);
        Court::create(['name' => 'Court 4', 'tournament_id' => 1, 'created_by' => 1]);
        Court::create(['name' => 'Court 5', 'tournament_id' => 1, 'created_by' => 1]);
        Court::create(['name' => 'Court 6', 'tournament_id' => 1, 'created_by' => 1]);

        Court::create(['name' => 'Court 1', 'tournament_id' => 2, 'created_by' => 1]);
        Court::create(['name' => 'Court 2', 'tournament_id' => 2, 'created_by' => 1]);
        Court::create(['name' => 'Court 3', 'tournament_id' => 2, 'created_by' => 1]);
        Court::create(['name' => 'Court 4', 'tournament_id' => 2, 'created_by' => 1]);
    }
}
