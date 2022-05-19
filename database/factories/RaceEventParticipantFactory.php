<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\RaceEventParticipant>
 */
class RaceEventParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'raceEventId' => \App\RaceEvent::all()->random(),
            'categoryId' => \App\Category::all()->random(),
            'athleteId' => \App\Athlete::all()->random(),
            'attended' => $this->faker->numberBetween(0, 1),
            'disqualified' => $this->faker->numberBetween(0, 1),
            'disqualifiedText' => $this->faker->text,
            'name' => $this->faker->name,
            'gender' => $this->faker->word,
            'countryId' => \App\Country::all()->random(),
            'raceEventTeamId' => \App\RaceEventTeam::all()->random(),
            'topResult' => $this->faker->numberBetween(0, 100)

        ];
    }
}
