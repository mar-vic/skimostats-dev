<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

use App\RaceEvent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::share('footerGrandeCourses', $this->getGrandeCourses());
    }

    public function getGrandeCourses() {
        $courses = [
            [
                'id' => 13,
                'name' => 'Pierra Menta',
            ],
            [
                'id' => 34,
                'name' => 'Adamello Ski Raid',
            ],
            [
                'id' => 43,
                'name' => 'Trofeo Mezzalama',
            ],
            [
                'id' => 37,
                'name' => 'Tour Du Rutor',
            ],
            [
                'id' => 36,
                'name' => 'Altitoy Ternua',
            ],
            [
                'id' => 39,
                'name' => 'Patrouille des Glaciers',
            ],
        ];

        $result = [];
        foreach ($courses as $course) {
            $row = (object)$course;
            $row->event = null;

            $lastEvent = RaceEvent::where('is_visible', true)
                // ->has('entries')
                ->whereNotNull('type')
                ->where('raceId', $row->id)
                ->orderBy('startDate', 'desc')
                ->first();

            if($lastEvent) {
                $row->event = $lastEvent;
            }

            $result[] = $row;
        }

        return $result;
    }
}
