<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\RaceEventTeam>
 */
class RaceEventTeamFactory extends Factory
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
            'teamId' => \App\Team::all()->random(),
            'raceEventId' => \App\RaceEvent::all()->random(),
            'categoryId' => \App\Category::all()->random(),
            'countryId' => \App\Country::all()->random()
        ];
    }
}
