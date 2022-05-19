<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Athlete>
 */
class AthleteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'firstName' => $this->faker->name,
            'lastName' => $this->faker->name,
            'gender' => $this->faker->word,
            'dateOfBirth' => $this->faker->date,
            'countryId' => \App\Country::all()->random(),
            'placeOfBirth' => $this->faker->country,
            'dateOfBirth' => $this->faker->date('Y-m-d', 'now'),
            'height' => $this->faker->numberBetween(120, 210),
            'weight' => $this->faker->numberBetween(60, 110),
            'category' => $this->faker->word,
            'clicks' => $this->faker->numberBetween(0, 10000),
            'attendsLausanne' => $this->faker->numberBetween(0, 1),
            'show_in_api' => $this->faker->numberBetween(0, 1)
        ];
    }
}
