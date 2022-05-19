<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\UpcomingRace>
 */
class UpcomingRaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'athleteId' => \App\Athlete::all()->random(),
            'eventId' => \App\RaceEvent::all()->random()
        ];
    }
}
