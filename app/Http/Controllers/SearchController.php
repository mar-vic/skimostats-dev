<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Athlete;
use App\RaceEvent;

class SearchController extends Controller
{
    public function search(Request $request) {
        $query = $request->q;

        $results = [];

        if (strlen($query) > 1) {
            $athletes = Athlete::where('firstName', 'LIKE', '%'.$query.'%')
                ->orWhere('lastName', 'LIKE', '%'.$query.'%')
                ->orWhereRaw('CONCAT(firstName, " ", lastName) LIKE ?', ['%' . $query . '%'])
                ->orWhereRaw('CONCAT(lastName, " ", firstName) LIKE ?', ['%' . $query . '%'])
                ->get();

            foreach($athletes as $athlete) {
                $results[] = ['link' => route('athletes.detail.slug', $athlete->slug), 'name'=>'Athlete - ' . $athlete->name];
            }

            $events = RaceEvent::where('is_visible', 1)->whereNotNull('type')->where('name', 'LIKE', '%'.$query.'%')->get();

            foreach($events as $event) {
                $results[] = ['link' => route('raceEventDetail.slug', $event->slug), 'name'=>'Race event - ' . $event->name . ' ('.$event->raceType->name.') - ' . (new Carbon($event->startDate))->isoFormat('DD. MM. YYYY')];
            }
        }

        return view('front.search', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
