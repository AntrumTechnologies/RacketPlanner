<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_organization_id' => 0,
            'name' => fake()->words(3, true),
            'datetime_start' => fake()->date('Y-m-d\TH:i'),
            'datetime_end' => fake()->date('Y-m-d\TH:i'),
            'description' => fake()->text(),
            'location' => fake()->streetAddress() .', '. fake()->city(),
            'location_link' => fake()->url(),
            'max_players' => fake()->randomDigit(),
            'enroll_until' => fake()->date('Y-m-d\TH:i'),
            'double_matches' => fake()->boolean(),
        ];
    }
}
