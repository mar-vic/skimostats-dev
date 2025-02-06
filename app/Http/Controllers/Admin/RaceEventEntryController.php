<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\RaceEventEntry;
use App\RaceEvent;
use App\Category;
use App\Athlete;
use App\Team;
use App\RaceEventTeam;
use App\RaceEventParticipant;
use App\RaceType;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class RaceEventEntryController extends Controller
{
    public function splitName($name) {
        $exp = explode(" ", $name);
        $firstName = array_pop($exp);
        $lastName = implode(" ", $exp);

        return [$lastName, $firstName];
    }

    public function update(Request $request, RaceEvent $event, Category $category, RaceEventEntry $entry) {

        $request->validate([
            'rank' => 'required',
            'entityId' => 'required_without:entityName',
            'entityName' => 'required_without:entityId',
            'time' => 'required',
        ]);

        // dd($request);
        // dd($entry);

        $entry->fill($request->only([
            'rank',
        ]));

        $raceType = RaceType::find($request->get('raceTypeId'));

        // dd($raceType);

        $name = '';

        if ($category->isTeam) {
            $team = null;

            if ($request->get('entityId')) {
                $team = Team::find($request->get('entityId'));
                if (!$team) {
                    return back()->withErrors([
                        'entityId' => 'Could not find team by ID',
                    ]);
                }
            } else {
                $name = $request->get('entityName');
                $findTeam = Team::where('name')->first();
                if ($findTeam) {
                    $team = $findTeam;
                } else {
                    $team = new Team();
                    $team->name = $name;
                    $team->save();
                }
            }

            $raceEventTeam = new RaceEventTeam();
            $raceEventTeam->name = $request->get('entityName') ?? $team->name;
            $raceEventTeam->teamId = $team->id;
            $raceEventTeam->raceEventId = $event->id;
            $raceEventTeam->categoryId = $category->id;
            $raceEventTeam->countryId = $team->countryId;
            $raceEventTeam->save();

            $entry->raceEventTeamId = $raceEventTeam->id;

            $name = $raceEventTeam->name;
        } else {
            $athlete = null;

            if ($request->get('entityId')) {
                $athlete = Athlete::find($request->get('entityId'));
                if (!$athlete) {
                    return back()->withErrors([
                        'entityId' => 'Could not find athlete by ID',
                    ]);
                }
            } else {
                list($lastName, $firstName) = $this->splitName($request->get('entityName'));

                $findAthlete = Athlete::where('firstName', $firstName)->where('lastName', $lastName)->first();
                if ($findAthlete) {
                    $athlete = $findAthlete;
                } else {
                    $athlete = new Athlete();
                    $athlete->firstName = $firstName;
                    $athlete->lastName = $lastName;
                    $athlete->save();
                }
            }

            $participant = new RaceEventParticipant();
            $participant->athleteId = $athlete->id;
            $participant->raceEventId = $event->id;
            $participant->categoryId = $category->id;
            if ($request->get('gender')) {
                $participant->gender = $request->get('gender');
            } else {
                $participant->gender = $athlete->gender;
            }
            $participant->name = $request->get('entityName') ?? $athlete->name;
            $participant->save();

            $entry->raceEventParticipantId = $participant->id;

            $name = $participant->name;
        }

        $entry->raceEventId = $event->id;
        $entry->categoryId = $category->id;
        $entry->timeRaw = $request->get('time');
        $entry->time = Helper::rawTimeToMillis($entry->timeRaw);

        // dd($entry);

        if ($request->has('raceEventStageId')) {
            $entry->raceEventStageId = $request->get('raceEventStageId');
        }

        // dd($entry);

        $entry->save();

        return back()->with('success', 'Sucessfully saved ' . $name . '.');
    }

    public function add(Request $request, RaceEvent $event, Category $category) {
        return $this->update($request, $event, $category, new RaceEventEntry());
    }

    public function delete(Request $request, RaceEventEntry $entry) {
        $name = $entry->participant ? $entry->participant->name : $entry->team->name;

        RaceEventTeam::where('id', $entry->raceEventTeamId)->delete();

        $entry->delete();

        return back()->with('success', 'Sucessfully deleted ' . $name . ' from results.');
    }

    public function updateSingle(Request $request, RaceEventEntry $entry) {
        $request->validate([
            'athleteId' => 'required|exists:athletes,id',
            'time' => 'required',
            'gender' => 'required',
            'rank' => 'required',
        ]);

        $athlete = Athlete::find($request->get('athleteId'));

        $entry->participant->athleteId = $athlete->id;
        $entry->participant->name = $athlete->name;
        $entry->participant->gender = $request->get('gender');
        $entry->participant->save();

        $entry->timeRaw = $request->get('time');
        $entry->time = Helper::rawTimeToMillis($request->get('time'));
        $entry->prependTime = $request->get('prependTime');
        $entry->rank = (int)$request->get('rank');

        $entry->status = !is_numeric($request->get('rank')) ? $request->get('rank') : null;

        $entry->save();

        return back();
    }

    public function updateSingleTeam(Request $request, RaceEventEntry $entry) {
        $request->validate([
            'teamId' => 'required',
            'athleteIds' => 'required',
            'time' => 'required',
            'rank' => 'required',
        ]);

        $teamId = $request->get('teamId');

        $athleteIds = array_map("trim", explode(",", $request->get('athleteIds')));
        $athletes = [];
        foreach($athleteIds as $athleteId) {
            $findSQL = Athlete::where('id', $athleteId);

            $nameParts = explode(" ", $athleteId);
            if (count($nameParts) > 1) {
                $firstName = $nameParts[1];
                $lastName = $nameParts[0];
                $findSQL->orWhere(
                    DB::raw("CONCAT(lastName,' ',firstName)"),
                    'LIKE',
                    '%'.$athleteId.'%'
                )
                ->orWhere(function($q) use ($firstName, $lastName) {
                    $q->where('lastName', 'LIKE', '%'.$lastName.'%')
                        ->where('firstName', 'LIKE', '%'.$firstName.'%');
                });
            }
            $find = $findSQL->first();
            $athletes[] = $find;
            if (!$find) {
                return back()->withErrors([
                    'athleteIds' => 'Athlete ID '.$athleteId.' does not exist.'
                ])->withInput();
            }
        }

        RaceEventParticipant::where('raceEventTeamId', $teamId)->delete();

        foreach($athletes as $athlete) {
            $participant = new RaceEventParticipant();
            $participant->athleteId = $athlete->id;
            $participant->raceEventTeamId = $teamId;
            $participant->name = $athlete->name;
            $participant->gender = $athlete->gender;
            $participant->attended = 1;
            $participant->countryId = $athlete->countryId;
            $participant->raceEventId = $entry->raceEventId;
            $participant->categoryId = $entry->categoryId;
            $participant->save();
        }

        $entry->timeRaw = $request->get('time');
        $entry->time = Helper::rawTimeToMillis($request->get('time'));
        $entry->prependTime = $request->get('prependTime');
        $entry->rank = (int)$request->get('rank');

        $entry->status = !is_numeric($request->get('rank')) ? $request->get('rank') : null;

        $entry->save();

        return back();
    }

    public function addSingleTeam(Request $request) {
        $request->validate([
            'categoryId' => 'required|exists:categories,id',
            'raceEventId' => 'required|exists:race_events,id',
            'athleteIds' => 'required',
            'time' => 'required',
            'rank' => 'required',
        ]);

        $eventId = $request->get('raceEventId');
        $categoryId = $request->get('categoryId');

        $teamName = 'Team';
        $teamCountryId = null;
        $athleteIds = array_map("trim", explode(",", $request->get('athleteIds')));
        $athletes = [];

        foreach($athleteIds as $athleteId) {
            $findSQL = Athlete::where('id', $athleteId);

            $nameParts = explode(" ", $athleteId);
            if (count($nameParts) > 1) {
                $firstName = $nameParts[1];
                $lastName = $nameParts[0];
                $findSQL->orWhere(
                    DB::raw("CONCAT(lastName,' ',firstName)"),
                    'LIKE',
                    '%'.$athleteId.'%'
                )
                ->orWhere(function($q) use ($firstName, $lastName) {
                    $q->where('lastName', 'LIKE', '%'.$lastName.'%')
                        ->where('firstName', 'LIKE', '%'.$firstName.'%');
                });
            }
            $find = $findSQL->first();
            $athletes[] = $find;
            if (!$find) {
                return back()->withErrors([
                    'athleteIds' => 'Athlete ID '.$athleteId.' does not exist.'
                ])->withInput();
            } else {
                if ($find->country && !$teamCountryId) {
                    $teamName = $find->country->name;
                    $teamCountryId = $find->country->id;
                }
            }
        }

        $team = new RaceEventTeam();
        $team->countryId = $teamCountryId;
        $team->name = $teamName;
        $team->raceEventId = $eventId;
        $team->categoryId = $categoryId;
        $team->save();

        $teamId = $team->id;

        foreach($athletes as $athlete) {
            $participant = new RaceEventParticipant();
            $participant->athleteId = $athlete->id;
            $participant->raceEventTeamId = $teamId;
            $participant->name = $athlete->name;
            $participant->gender = $athlete->gender;
            $participant->attended = 1;
            $participant->countryId = $athlete->countryId;
            $participant->raceEventId = $eventId;
            $participant->categoryId = $categoryId;
            $participant->save();
        }

        $entry = new RaceEventEntry();

        $entry->raceEventId = $eventId;
        $entry->categoryId = $categoryId;
        $entry->raceEventTeamId = $teamId;
        $entry->timeRaw = $request->get('time');
        $entry->time = Helper::rawTimeToMillis($request->get('time'));
        $entry->prependTime = $request->get('prependTime');
        $entry->rank = (int)$request->get('rank');

        $entry->status = !is_numeric($request->get('rank')) ? $request->get('rank') : null;

        $entry->save();

        return back();
    }
}
