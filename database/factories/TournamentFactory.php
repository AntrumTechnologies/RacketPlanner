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
            'datetime_start' => fake()->dateTimeBetween('+2 months', '+3 months'),
            'datetime_end' => fake()->dateTimeBetween('+3 months', '+4 months'),
            'description' => fake()->text(),
            'location' => fake()->streetAddress() .', '. fake()->city(),
            'location_link' => fake()->url(),
            'max_players' => fake()->numberBetween(2, 42),
            'enroll_until' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'double_matches' => fake()->boolean(),
        ];
    }
}
