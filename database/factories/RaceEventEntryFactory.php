<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\RaceEventEntry>
 */
class RaceEventEntryFactory extends Factory
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
            'raceEventStageId' => \App\RaceEventStage::all()->random(),
            'categoryId' => \App\Category::all()->random(),
            'raceEventParticipantId' => \App\RaceEventParticipant::all()->random(),
            'raceEventTeamId' => \App\RaceEventTeam::all()->random(),
            'timeRaw' => $this->faker->word,
            'time' => $this->faker->numberBetween(10, 80),
            'rank' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->word,
            'prependTime' => $this->faker->word
        ];
    }
}
