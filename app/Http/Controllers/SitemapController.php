<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Race;
use App\RaceEvent;
use App\RaceEventEntry;
use App\Athlete;
use App\Category;

class SitemapController extends Controller
{
    public function sitemap() {
        $map = [
            [
                'loc' => asset('/'),
                'changefreq' => 'daily',
                'priority' => 0.8,
            ],
            [
                'loc' => route('races'),
                'changefreq' => 'weekly',
                'priority' => 0.8,
            ],
            [
                'loc' => route('athletes'),
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ],
            [
                'loc' => route('statistics'),
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ],
            [
                'loc' => route('rankings'),
                'changefreq' => 'weekly',
                'priority' => 0.6,
            ],
            [
                'loc' => route('about-us'),
                'changefreq' => 'monthly',
                'priority' => 0.4,
            ],
            [
                'loc' => route('cookies'),
                'priority' => 0.3,
            ],
            [
                'loc' => route('contributions'),
                'priority' => 0.3,
            ],
        ];

        $this->addIsmfRankings($map);

        $this->addIsmfWorldCup($map);
        $this->addRacesYears($map);
        $this->addRaces($map);

        $this->addAthleteCountries($map);
        $this->addAthletes($map);

        return response($this->create($map), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function addIsmfRankings(&$map) {
        $categories = Category::whereIn('id', [
            1,2,3,4,5,6
        ])->get();

        for ($year = 2018; $year <= date("Y"); $year++) {
            foreach($categories as $cat) {
                $thisYear = date("Y") == $year;
                $map[] = [
                    'loc' => route('rankings.year', ['ismf', $year, $cat->slug]),
                    'changefreq' => $thisYear ? 'weekly' : 'yearly',
                    'priority' => $thisYear ? 0.7 : 0.5,
                ];
            }
        }
    }

    public function addIsmfWorldCup(&$map) {
        $years = array_map(
            function ($item) {
                return $item->year;
            },
            DB::table('race_events')
                ->select(DB::raw('YEAR(startDate) as year'))
                ->where('is_visible', true)
                ->whereIn('raceId', Race::getIsmfWorldCupIds())
                ->groupBy(DB::raw('YEAR(startDate)'))
                ->orderBy('year', 'asc')
                ->get()
                ->toArray()
        );
        $currentYear = date("Y");
        foreach($years as $year) {
            $map[] = [
                'loc' => route('world-cup.year', $year),
                'changefreq' => $year >= $currentYear ? 'weekly' : 'monthly',
                'priority' => $year >= $currentYear ? 0.8 : 0.5,
            ];
        }
    }

    public function addRacesYears(&$map) {
        $years = DB::table('race_events')
            ->select(DB::raw('YEAR(startDate) as year'))
            ->where('is_visible', true)
            ->groupBy(DB::raw('YEAR(startDate)'))
            ->get();

        foreach ($years as $year) {
            if ($year->year == 0) {
                continue;
            }
            $map[] = [
                'loc' => route('races.year', $year->year),
                'changefreq' => 'monthly',
                'priority' => 0.4,
            ];
        }
    }

    public function addRaces(&$map) {
        $races = RaceEvent::where('is_visible', 1)->whereNotNull('type')->get();

        foreach($races as $event) {
            $lastmod = substr($event->updated_at, 0, 10);

            $lastEntry = RaceEventEntry::where('raceEventId', $event->id)->orderBy('updated_at', 'desc')->first();
            if ($lastEntry && $event->updated_at < $lastEntry->updated_at) {
                $lastmod = substr($lastEntry->updated_at, 0, 10);
            }

            $map[] = [
                'loc' => route('raceEventDetail.slug', $event->slug),
                'lastmod' => $lastmod,
                'priority' => 0.5,
            ];

            foreach($event->categories as $cat) {
                $map[] = [
                    'loc' => route('raceEventDetail.slug.category', [$event->slug, $cat->slug]),
                    'lastmod' => $lastmod,
                    'priority' => 0.5,
                ];
            }
        }
    }

    public function addAthleteCountries(&$map) {
        $countries = \DB::table('countries')
            ->select(
                'countries.id',
                'countries.name',
                'countries.code',
                \DB::raw('COUNT(athletes.id) as athleteCount')
            )
            ->join('athletes', 'athletes.countryId', 'countries.id')
            ->groupBy('countries.id')
            ->get();

        foreach ($countries as $country) {
            $map[] = [
                'loc' => route('athletes') . "?country=" .$country->code,
                'changefreq' => 'monthly',
                'priority' => 0.4,
            ];
        }
    }

    public function addAthletes(&$map) {
        $athletes = Athlete::all();

        foreach($athletes as $athlete) {
            $map[] = [
                'loc' => route('athletes.detail.slug', $athlete->slug),
                'changefreq' => 'weekly',
                'priority' => $athlete->slug == 'kilian-jornet-burgada' ? 0.6 : 0.5,
            ];
        }
    }

    public function create($map) {
        $res = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($map as $item) {
            $res.= "
    <url>";
            if (isset($item['loc'])) {
                $res.= "
        <loc>".$item['loc']."</loc>
                ";
            }
            if (isset($item['lastmod'])) {
                $res.= "
        <lastmod>".$item['lastmod']."</lastmod>
                ";
            }
            if (isset($item['changefreq'])) {
                $res.= "
        <changefreq>".$item['changefreq']."</changefreq>
                ";
            }
            if (isset($item['priority'])) {
                $res.= "
        <priority>".$item['priority']."</priority>
                ";
            }

            $res.= "
    </url>
            ";
        }

        $res.= '
</urlset>';

        return $res;
    }

}
