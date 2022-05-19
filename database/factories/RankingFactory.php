<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Ranking>
 */
class RankingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => $this->faker->numberBetween(1, 100),
            'points' => $this->faker->numberBetween(1, 100),
            'athleteId' => \App\Athlete::all()->random(),
            'participantId' => \App\RaceEventParticipant::all()->random(),
            'categoryId' => \App\Category::all()->random(),
            'raceTypeId' => \App\RaceType::all()->random(),
            'addedBy' => \App\User::all()->random(),
            'reason' => $this->faker->text,
            'obtainedAt' => $this->faker->dateTime,
            'raceId' => \App\Race::all()->random(),
            'raceEventId' => \App\RaceEvent::all()->random(),
            'rank' => $this->faker->numberBetween(1, 20),
            'rankingCategoryId' => \App\RankingCategory::all()->random()
        ];
    }
}
