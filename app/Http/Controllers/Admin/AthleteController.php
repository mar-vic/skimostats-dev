<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\AthleteTopResult;
use App\Athlete as Entry;
use App\Country;
use App\RaceEventParticipant;


class AthleteController extends Controller
{
    private $slug = 'athletes';
    private $entityName = 'Athletes';

    public function list(Request $request) {
        $entries = Entry::orderBy('lastName', 'asc')->orderBy('firstName', 'asc');

        if ($request->has('filter')) {
            $filter = $request->get('filter');

            $entries->where(function($qr) use ($filter) {
                $qr->where('lastName', 'LIKE', '%'.$filter.'%')
                    ->orWhere('firstName', 'LIKE', '%'.$filter.'%');
            });
        }
        if ($request->has('show_in_api')) {
            $filter = $request->get('show_in_api');

            $entries->where('show_in_api', true);
        }

        return view('admin.athletes.index', [
            'entries' => $entries->paginate(20)->onEachSide(7),
            'slug' => $this->slug,
            'entityName' => $this->entityName,
        ]);
    }

    public function add(Request $request) {
        return $this->edit($request, new Entry(), false);
    }

    public function edit(Request $request, Entry $entry, $edit = true) {
        $links = json_decode($entry->socialLinks, true);

        return view('admin.athletes.edit', [
            'edit' => $edit,
            'entry' => $entry,
            'socialPlatforms' => [
                'strava',
                'facebook',
                'twitter',
                'instagram',
                'web',
            ],
            'socialLinks' => $links,
            'slug' => $this->slug,
            'entityName' => $this->entityName,
            'countries' => Country::orderBy('name', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Entry $entry) {
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:10000',
        ]);

        $links = $request->get('links');

        $entry->fill($request->only([
            'firstName',
            'lastName',
            'placeOfBirth',
            'favoriteRace',
            'favoriteRaceId',
            'weight',
            'height',
            'category',
            'gender',
        ]));

        $entry->attendsLausanne = $request->filled('attendsLausanne');
        $entry->show_in_api = $request->filled('show_in_api');
        $entry->socialLinks = json_encode($links);

        foreach ($request->get('topresult') as $i => $topresult) {
            if (!$topresult) {
                continue;
            }
            $result = AthleteTopResult::where('athleteId', $entry->id)->where('position', $i)->first();
            if (!$result) {
                $result = new AthleteTopResult();
            }
            $place = $request->get('topresultplace')[$i];
            $result->athleteId = $entry->id;
            $result->position = $i;
            $result->place = $place;
            $result->name = $topresult;
            $result->save();
        }

        if ($request->countryId != 0) {
            $entry->countryId = $request->countryId;
        }

        if ($request->get('dateOfBirth')) {
            $entry->dateOfBirth = date('Y-m-d', strtotime($request->get('dateOfBirth')));
        }

        $entry->save();

        if ($request->hasFile('image')) {
            $entry->setImage($request->file('image'));
        }

        return back()->with([
            'success' => 'Sucessfully saved ' . $entry->name . '.',
        ]);
    }

    public function create(Request $request) {
        return $this->update($request, new Entry());
    }

    public function delete(Request $request, Entry $entry) {
        $name = $entry->name;
        $entry->delete();

        return back()->with([
            'success' => 'Successfully deleted ' . $name . '.'
        ]);
    }


    public function athleteRaces(Request $request, Entry $athlete) {
        $filter = $request->get('filter');
        $entriesBuilder = RaceEventParticipant::join('race_events', 'race_events.id', '=', 'race_event_participants.raceEventId')
            ->leftJoin('rankings', 'rankings.participantId', '=', 'race_event_participants.id')
            ->select('race_event_participants.*', 'rankings.points')
            ->where('race_event_participants.athleteId', $athlete->id)
            ->groupBy('race_event_participants.id')
            ->orderByRaw('ISNULL(race_event_participants.topResult) asc')
            ->orderBy('race_event_participants.topResult', 'asc')
            ->orderBy('rankings.points', 'desc')
            ->orderBy('rankings.obtainedAt', 'desc')
            ->orderBy('race_events.startDate', 'desc');

        if ($filter) {
            $entriesBuilder->where('race_events.name', 'LIKE', '%'.$filter.'%');
        }

        $entries = $entriesBuilder->get();

        return view('admin.athletes.athlete-races', [
            'slug' => $this->slug,
            'entityName' => $this->entityName,
            'athlete' => $athlete,
            'entries' => $entries,
        ]);
    }

    public function updateTopResults(Request $request) {
        $request->validate([
            'athleteId' => 'required',
            'topresults' => 'array',
        ]);

        $athleteId = $request->get('athleteId');

        foreach($request->get('topresults') as $participantId => $val) {
            $participant = RaceEventParticipant::where('athleteId', $athleteId)->where('id', $participantId)->first();
            if ($participant) {
                if ($val == 0 && $participant->topResult) {
                    $participant->topResult = null;
                    $participant->save();
                }
                if ($val != 0) {
                    $participant->topResult = $val;
                    $participant->save();
                }
            }
        }

        return back()->with('success', 'Top results were updated.');
    }
}
