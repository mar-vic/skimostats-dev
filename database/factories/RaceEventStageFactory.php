<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\RaceEventStage>
 */
class RaceEventStageFactory extends Factory
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
            'stage' => $this->faker->numberBetween(1, 5),
            'name' => $this->faker->word,
            'description' => $this->faker->text
        ];
    }
}
