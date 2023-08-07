<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('players')->truncate();

        Player::create(['user_id' => 1, 'tournament_id' => 1]);
        Player::create(['user_id' => 2, 'tournament_id' => 1]);
        Player::create(['user_id' => 3, 'tournament_id' => 2]);
        Player::create(['user_id' => 4, 'tournament_id' => 2]);
        Player::create(['user_id' => 5, 'tournament_id' => 1]);
        Player::create(['user_id' => 6, 'tournament_id' => 2]);
        Player::create(['user_id' => 7, 'tournament_id' => 2]);
        Player::create(['user_id' => 8, 'tournament_id' => 2]);
        Player::create(['user_id' => 9, 'tournament_id' => 2]);
        Player::create(['user_id' => 10, 'tournament_id' => 1]);
        Player::create(['user_id' => 11, 'tournament_id' => 2]);
        Player::create(['user_id' => 12, 'tournament_id' => 2]);
        Player::create(['user_id' => 13, 'tournament_id' => 2]);
        Player::create(['user_id' => 14, 'tournament_id' => 1]);
        Player::create(['user_id' => 15, 'tournament_id' => 1]);
        Player::create(['user_id' => 16, 'tournament_id' => 1]);
        Player::create(['user_id' => 17, 'tournament_id' => 2]);
        Player::create(['user_id' => 18, 'tournament_id' => 2]);
        Player::create(['user_id' => 19, 'tournament_id' => 2]);
        Player::create(['user_id' => 20, 'tournament_id' => 2]);
        Player::create(['user_id' => 21, 'tournament_id' => 1]);
        Player::create(['user_id' => 22, 'tournament_id' => 2]);
        Player::create(['user_id' => 23, 'tournament_id' => 1]);
        Player::create(['user_id' => 24, 'tournament_id' => 2]);
        Player::create(['user_id' => 25, 'tournament_id' => 1]);
        Player::create(['user_id' => 26, 'tournament_id' => 2]);
        Player::create(['user_id' => 27, 'tournament_id' => 2]);
        Player::create(['user_id' => 28, 'tournament_id' => 2]);
        Player::create(['user_id' => 29, 'tournament_id' => 1]);
        Player::create(['user_id' => 30, 'tournament_id' => 2]);
        Player::create(['user_id' => 31, 'tournament_id' => 1]);
        Player::create(['user_id' => 32, 'tournament_id' => 1]);
        Player::create(['user_id' => 33, 'tournament_id' => 2]);
        Player::create(['user_id' => 34, 'tournament_id' => 2]);
        Player::create(['user_id' => 35, 'tournament_id' => 1]);
        Player::create(['user_id' => 36, 'tournament_id' => 2]);
        Player::create(['user_id' => 37, 'tournament_id' => 1]);
        Player::create(['user_id' => 38, 'tournament_id' => 1]);
        Player::create(['user_id' => 39, 'tournament_id' => 2]);
        Player::create(['user_id' => 40, 'tournament_id' => 2]);
        Player::create(['user_id' => 41, 'tournament_id' => 1]);
        Player::create(['user_id' => 42, 'tournament_id' => 1]);
        Player::create(['user_id' => 43, 'tournament_id' => 1]);
        Player::create(['user_id' => 44, 'tournament_id' => 1]);
        Player::create(['user_id' => 45, 'tournament_id' => 1]);
        Player::create(['user_id' => 46, 'tournament_id' => 1]);
        Player::create(['user_id' => 47, 'tournament_id' => 1]);
        Player::create(['user_id' => 48, 'tournament_id' => 1]);
        Player::create(['user_id' => 49, 'tournament_id' => 1]);
        Player::create(['user_id' => 50, 'tournament_id' => 1]);
        Player::create(['user_id' => 51, 'tournament_id' => 1]);
        Player::create(['user_id' => 52, 'tournament_id' => 1]);
        Player::create(['user_id' => 53, 'tournament_id' => 1]);
        Player::create(['user_id' => 54, 'tournament_id' => 1]);
        Player::create(['user_id' => 55, 'tournament_id' => 1]);
        Player::create(['user_id' => 56, 'tournament_id' => 1]);
        Player::create(['user_id' => 57, 'tournament_id' => 1]);
        Player::create(['user_id' => 58, 'tournament_id' => 1]);
        Player::create(['user_id' => 59, 'tournament_id' => 1]);
        Player::create(['user_id' => 60, 'tournament_id' => 1]);
        Player::create(['user_id' => 61, 'tournament_id' => 1]);
        Player::create(['user_id' => 62, 'tournament_id' => 1]);
        Player::create(['user_id' => 63, 'tournament_id' => 1]);
        Player::create(['user_id' => 64, 'tournament_id' => 1]);
        Player::create(['user_id' => 65, 'tournament_id' => 1]);
        Player::create(['user_id' => 66, 'tournament_id' => 1]);
        Player::create(['user_id' => 67, 'tournament_id' => 1]);
    }
}
