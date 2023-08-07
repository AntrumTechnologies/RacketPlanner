<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Player;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TournamentsTableSeeder::class);
        $this->command->info('Tournaments table seeded');

        $this->call(UsersTableSeeder::class);
        $this->command->info('Users table seeded');

        $this->call(PlayersTableSeeder::class);
        $this->command->info('Players table seeded');
    }
}