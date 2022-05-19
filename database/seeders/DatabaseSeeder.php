<?php

namespace Database\Seeders;

use App\RaceEvent;
use App\RaceEventCategory;
use CreateUpcomingRacesTable;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // Initial Seeders (some static data used in production)
            UsersTableSeeder::class,
            CountriesTableSeeder::class,
            RaceTypesTableSeeder::class,
            CategoriesTableSeeder::class,

            // My Seeders (fake data for testing purposes)
            AthletesTableSeeder::class,
            RankingCategoriesTableSeeder::class,
            RacesTableSeeder::class,
            RaceEventsTableSeeder::class,
            TeamsTableSeeder::class,
            TeamAthletesTableSeeder::class,
            RaceEventTeamsTableSeeder::class,
            RaceEventParticipantsTableSeeder::class,
            RaceEventCategoriesTableSeeder::class,
            RaceEventStagesTableSeeder::class,
            RankingsTableSeeder::class,
            RaceEventEntriesTableSeeder::class,
            UpcomingRacesTableSeeder::class
        ]);
    }
}
