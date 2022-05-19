<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\TeamAthlete>
 */
class TeamAthleteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'athleteId' => \App\Athlete::all()->random(),
            'teamId' => \App\Team::all()->random(),
            'countryId' => \App\Country::all()->random(),
            'positionName' => $this->faker->word,
            'position' => $this->faker->numberBetween(1, 20)
        ];
    }
}
