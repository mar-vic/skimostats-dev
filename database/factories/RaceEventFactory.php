<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\RaceEvent>
 */
class RaceEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-5 years', '+2 months');
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->text,
            'raceId' => \App\Race::all()->random(),
            'startDate' => $startDate,
            'endDate' => $this->faker->dateTime,
            'year' => $this->faker->numberBetween(2000, 2022),
            'elevation' => $this->faker->numberBetween(0, 100),
            'place' => $this->faker->country,
            'type' => $this->faker->numberBetween(100, 10000),
            'hasStages' => $this->faker->numberBetween(0,1),
            'isTeam' => $this->faker->numberBetween(0,1),
            'is_visible' => $this->faker->numberBetween(0,1),
            'countryId' => \App\Country::all()->random(),
            'rankingCategoryId' => \App\RankingCategory::all()->random(),
            'stageNumber' => $this->faker->numberBetween(1, 5),
            'isGeneralClassification' => $this->faker->numberBetween(0,1)
        ];
    }
}
