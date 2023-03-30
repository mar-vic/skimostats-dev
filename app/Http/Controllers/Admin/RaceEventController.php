<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Athlete;
use App\Category;
use App\Country;
use App\Team;
use App\Race;
use App\RaceEvent as Entry;
use App\RaceEventCategory;
use App\RaceEventEntry;
use App\RaceEventParticipant;
use App\RaceEventStage;
use App\RaceEventTeam;
use App\RaceType;
use App\RankingCategory;

use Helper;

class RaceEventController extends Controller
{
    private $slug = 'race-events';
    private $entityName = 'Race events';

    public function list(Request $request, Race $race) {
        $entries = Entry::orderBy('id', 'desc')->where('raceId', $race->id)->whereNull('parent');

        if ($request->has('filter')) {
            $filter = $request->get('filter');

            $entries->where('name', 'LIKE', '%'.$filter.'%');
        }

        return view('admin.'.$this->slug.'.index', [
            'entries' => $entries->get(),
            'slug' => $this->slug,
            'race' => $race,
            'raceEvent' => null,
            'entityName' => $this->entityName,
        ]);
    }

    public function listSubevents(Request $request, Entry $raceEvent) {
        $entries = Entry::orderBy('id', 'desc')->where('parent', $raceEvent->id);

        if ($request->has('filter')) {
            $filter = $request->get('filter');

            $entries->where('name', 'LIKE', '%'.$filter.'%');
        }

        return view('admin.'.$this->slug.'.index', [
            'entries' => $entries->get(),
            'slug' => $this->slug,
            'race' => $raceEvent->race,
            'raceEvent' => $raceEvent,
            'entityName' => $this->entityName,
        ]);
    }

    public function addSubevent(Request $request, Entry $raceEvent) {
        $entry = new Entry();
        $entry->name = $raceEvent->name;

        return $this->edit($request, $entry, $raceEvent->race, $raceEvent, false);
    }

    public function editSubevent(Request $request, Entry $raceEvent, Entry $entry) {
        return $this->edit($request, $entry, $raceEvent->race, $raceEvent, true);
    }

    public function add(Request $request, Race $race) {
        $entry = new Entry();
        $entry->name = $race->name . " " . date('Y');
        return $this->edit($request, $entry, $race, null, false);
    }

    public function edit(Request $request, Entry $entry, Race $race, Entry $raceEvent = null, $edit = true) {
        if (!$race->id) {
            $race = $entry->race;
        }

        $categories = Category::all();
        $countries = Country::orderBy('name', 'asc')->get();

        // dd('admin.'.$this->slug.'.edit');

        return view('admin.'.$this->slug.'.edit', [
            'edit' => $edit,
            'entry' => $entry,
            'race' => $race,
            'parentEvent' => $enty->eventParent ?? $raceEvent,
            'categories' => $categories,
            'raceTypes' => RaceType::all(),
            'slug' => $this->slug,
            'entityName' => $this->entityName,
            'countries' => $countries,
            'rankingCategories' => RankingCategory::orderBy('position','asc')->orderBy('id', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Entry $entry, Race $race, $edit = true) {
        $request->validate([
            'name' => 'required',
            // 'place' => 'required',
            'startDate' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:4096',
        ]);

        // dd($request->get('category'));

        $entry->fill($request->only([
            'name',
            'optionalName',
            'description',
            'place',
            'elevation',
        ]));

        $parentEvent = $request->has('parent') ? Entry::find($request->get('parent')) : null;


        if ($parentEvent && $parentEvent->hasStages()) {
            $entry->isGeneralClassification = $request->filled('isGeneralClassification');
            $entry->stageNumber = $request->filled('isGeneralClassification') ? null : $request->get('stageNumber');
        } else {
            $entry->hasStages = $request->filled('hasStages');
        }

        if ($request->countryId) {
            $entry->countryId = $request->countryId;
        }

        $entry->startDate = date("Y-m-d H:i:s", strtotime($request->get('startDate')));
        $entry->year = date("Y", strtotime($request->get('startDate')));

        $entry->is_visible = $request->filled('isVisible');

        if ($request->filled('endDate')) {
            $entry->endDate = date("Y-m-d H:i:s", strtotime($request->get('endDate')));
        }

        if ($race->id) {
            $entry->raceId = $race->id;
        }

        $entry->isTeam = false;

        if ($request->has('raceType')) {
            $theType = RaceType::find($request->get('raceType'));
            if ($theType) {
                $entry->type = $request->get('raceType');

                if ($theType->isTeamType()) {
                    $entry->isTeam = true;
                }
            }
        }

        if ($request->has('parent')) {
            if(Entry::find($request->get('parent'))) {
                $entry->parent = $request->get('parent');
            }
        }

        $entry->rankingCategoryId = $request->get('rankingCategoryId') ?? null;

        $entry->save();

        if ($request->filled('category')) {
            RaceEventCategory::where('raceEventId', $entry->id)->delete();

            if ($entry->canHaveCategories()) {
                foreach ($request->get('category') as $catId => $val) {
                    $find = RaceEventCategory::where('raceEventId', $entry->id)
                        ->where('categoryId', $catId)
                        ->first();
                    if (!$find) {
                        $newCat = new RaceEventCategory();
                        $category = Category::find($catId);
                        $newCat->fill([
                            'raceEventId' => $entry->id,
                            'categoryId' => $category->id,
                        ]);
                        $newCat->save();
                    }
                }
            }
        }


        if ($request->hasFile('image')) {
            $entry->setImage($request->file('image'));
        }

        return redirect($entry->hasParent() ? route('admin.race-events.list-subevents', $entry->parent) : route('admin.race-events.list', $entry->raceId))->with([
            'success' => 'Sucessfully saved ' . $entry->name . '.',
        ]);
    }

    public function create(Request $request, Race $race) {
        return $this->update($request, new Entry(), $race, false);
    }

    public function newStage(Request $request, Entry $entry) {
        $lastStage = RaceEventStage::where('raceEventId', $entry->id)->orderBy('stage', 'desc')->first();
        $newStage = new RaceEventStage();
        $newStage->raceEventId = $entry->id;
        $newStage->stage = $lastStage ? $lastStage->stage + 1 : 1;
        $newStage->save();

        return back();
    }

    public function deleteStage(Request $request, RaceEventStage $stage) {
        $stage->delete();
        return back();
    }

    public function show(Request $request, Entry $entry, int $stage = 0) {
        $categories = $entry->categories;
        $categoryId = $categories ? $categories[0]->id : 0;

        if ($request->has('raceCategory')) {
            $categoryId = $request->get('raceCategory');
        }

        $selectedStage = $stage ? RaceEventStage::where('raceEventId', $entry->id)->where('stage', $stage)->first() : false;

        $stages = RaceEventStage::where('raceEventId', $entry->id)->get();

        $entriesBuilder = RaceEventEntry::where('raceEventId', $entry->id)
            ->where('categoryId', $categoryId)
            ->orderBy('status', 'asc')
            ->orderBy('rank', 'asc');

        if ($selectedStage) {
            $entriesBuilder->where('raceEventStageId', $selectedStage->id);
        }
        $entries = $entriesBuilder->get();

        return view('admin.race-events.show', [
            'entry' => $entry,
            'race' => $entry->race,
            'stages' => $stages,
            'selectedRaceCategory' => Category::find($categoryId),
            'selectedStage' => $selectedStage,

            'raceEventEntries' => $entries,
            'firstRaceEventEntry' => count($entries) > 0 ? $entries[0] : null,

            'slug' => $this->slug,
            'entityName' => $this->entityName,
        ]);
    }

    public function exportEntriesToCsv(Request $request, Entry $raceEvent, Category $category) {
        $result = [];

        $entries = RaceEventEntry::where('raceEventId', $raceEvent->id)->where('categoryId', $category->id)->get();

        $result[] = '""';
        foreach($entries as $entry) {
            $participants = $entry->getParticipants();
            $names = [];
            $ids = [];
            foreach($participants as $participant) {

                if ($participant->athlete) {
                    $ids[] = $participant->athlete->id;
                }
                $names[] = $participant->athlete ? $participant->athlete->lastName . ' ' . $participant->athlete->firstName : $participant->name;
            }
            $item = [
                '"' . ($entry->status ?? $entry->rank) . '"',
                '"' . implode(", ", $ids) . '"',
                '"' . implode(", ", $names) . '"',
                '',
                '"' . ($category->gender === 'male' ? 'Homme' : 'Femme') . '"',
                '',
                '"' . ($entry->prependTime ?? '') . '"',
                '"' . Helper::millisToTime($entry->time) . '"'
            ];

            $result[] = implode(";",$item);
        }

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.str_replace('"',"'",$raceEvent->name . ' - ' . $raceEvent->raceType->name . ' - ' . $category->name).'.csv"',
        );

        return response()->make(rtrim(implode("\n",$result), "\n"), 200, $headers);
    }

    public function splitName($name) {
        $exp = explode(" ", $name);
        $firstName = array_pop($exp);
        $lastName = implode(" ", $exp);

        return [$lastName, $firstName];
    }

    // status/rank, athlete ids, athlete names (lastName firstName), _, gender (Homme), ...., prependTime, timeRaw
    public function import(Request $request, Entry $raceEvent) {
        $request->validate([
            'import' => 'required|file',
            'raceCategoryId' => 'required',
        ]);

        $delimiter = $request->get('delimiter') ?? ';';

        $raceCategoryId = $request->get('raceCategoryId');

        $raceCategory = Category::find($raceCategoryId);
        $categoryGender = $raceCategory->gender;

        $raceEventStage = $request->has('raceEventStageId') ? RaceEventStage::find($request->get('raceEventStageId')) : false;

        $file = $request->file('import');

        $csv = array_map(function($row) use ($delimiter) {
            return str_getcsv($row, $delimiter);
        }, file($file->getRealPath()));

        $raceEventEntries = [];

        if ($raceEvent->isTeamRace()) {
            for ($i = 1; $i < count($csv); $i++) {
                $timeRaw = $csv[$i][count($csv[$i]) - 1];
                $time = Helper::rawTimeToMillis($timeRaw);

                $eventParticipantsToCreate = [];

                if (count($csv[$i]) < 3) {
                    continue;
                }

                $ids = [];
                if ($csv[$i][1]) { // Athlete ids, separated with commas
                    $ids = array_map("trim", explode(",", $csv[$i][1]));

                    foreach($ids as $athleteId) {
                        $eventParticipant = Athlete::find($athleteId);
                        if ($eventParticipant) {
                            $eventParticipantsToCreate[] = $eventParticipant;
                        }
                    }
                }

                if ($csv[$i][2]) { // Athlete names
                    $names = array_map("trim", explode(",", $csv[$i][2]));

                    foreach ($names as $name) {
                        list($lastName, $firstName) = $this->splitName($name);
                        $skip = false;

                        foreach ($eventParticipantsToCreate as $eventParticipant) {
                            if ($eventParticipant->firstName == $firstName && $eventParticipant->lastName == $lastName) {
                                $skip = true;
                            }
                        }

                        if ($skip) {
                            continue;
                        }

                        $searchForAthlete = Athlete::withFullName($firstName, $lastName)->whereNotIn('id', $ids)->first();

                        $eventParticipantsToCreate[] = $searchForAthlete ?? Athlete::create([
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'gender' => $categoryGender,
                        ]);


                    }
                }

                if (count($eventParticipantsToCreate) == 0) {
                    continue;
                }
                // Create RaceEventTeam

                $raceEventTeam = new RaceEventTeam();
                $raceEventTeam->name = $eventParticipantsToCreate[0] && $eventParticipantsToCreate[0]->country ? $eventParticipantsToCreate[0]->country->name : 'Unnamed team';
                $raceEventTeam->countryId = $eventParticipantsToCreate[0] && $eventParticipantsToCreate[0]->country ? $eventParticipantsToCreate[0]->country->id : null;
                $raceEventTeam->raceEventId = $raceEvent->id;
                $raceEventTeam->categoryId = $raceCategoryId;
                $raceEventTeam->save();


                // DNF, DSQ
                $status = is_numeric($csv[$i][0]) ? null : $csv[$i][0];

                // Create RaceEventParticipant entries
                foreach($eventParticipantsToCreate as $eventParticipant) {
                    $newParticipant = new RaceEventParticipant();
                    $newParticipant->name = $eventParticipant->lastName . " " . $eventParticipant->firstName;
                    $newParticipant->raceEventId = $raceEvent->id;
                    $newParticipant->categoryId = $raceCategoryId;
                    $newParticipant->athleteId = $eventParticipant->id;
                    $newParticipant->attended = 1;
                    $newParticipant->disqualified = $status == 'DSQ';
                    $newParticipant->gender = $eventParticipant->gender;
                    $newParticipant->countryId = $eventParticipant->countryId;
                    $newParticipant->raceEventTeamId = $raceEventTeam->id;
                    $newParticipant->save();
                }

                $rank = (int)$csv[$i][0];

                $raceEventEntries[] = [
                    'raceEventId' => $raceEvent->id,
                    'categoryId' => $raceCategoryId ?? null,
                    'raceEventTeamId' => $raceEventTeam->id,
                    'raceEventStageId' => $raceEventStage ? $raceEventStage->id : null,
                    'timeRaw' => $timeRaw,
                    'time' => $time,
                    'rank' => $status ? null : $rank,
                    'status' => $status,
                    'created_at' => \DB::raw('NOW()'),
                    'updated_at' => \DB::raw('NOW()'),
                ];
            }
        } else {
            // dd($csv);
            for ($i = 1; $i < count($csv); $i++) {
                if (count($csv[$i]) < 4) {
                    continue;
                }
                $timeRaw = $csv[$i][count($csv[$i]) - 1];
                $time = Helper::rawTimeToMillis($timeRaw);

                $name = $this->splitName($csv[$i][2]);
                $gender = $csv[$i][4] ? ($csv[$i][4] == 'Homme' ? 'male' : 'female') : 'male';

                $athletePreFilled = null;
                if ($csv[$i][1]) {
                    $athletePreFilled = Athlete::find((int)$csv[$i][1]);

                    if ($athletePreFilled) {
                        if(!$name[0] || !$name[1]) {
                            $name[1] = $athletePreFilled->firstName;
                            $name[0] = $athletePreFilled->lastName;
                        }

                        if (!$csv[$i][4]) {
                            $gender = $athletePreFilled->gender;
                        }
                    }
                }

                $searchForAthlete = $athletePreFilled ?? Athlete::withFullName($name[1], $name[0])->first();

                $athlete = $searchForAthlete ?? Athlete::create([
                    'firstName' => $name[1],
                    'lastName' => $name[0],
                    'gender' => $categoryGender ?? $gender,
                ]);


                $eventParticipant = RaceEventParticipant::create([
                    'name' => $name[0] . " " . $name[1],
                    'athleteId' => $athlete->id,
                    'attended' => true,
                    'disqualified' => false,
                    'raceEventId' => $raceEvent->id,
                    'categoryId' => $raceCategoryId ?? null,
                    'gender' => $gender,
                ]);

                $rank = (int)$csv[$i][0];

                $prependTime = null;

                if ($csv[$i][count($csv[$i]) - 2]) {
                    $prependTime = $csv[$i][count($csv[$i]) - 2];
                }

                // DNF, DSQ
                $status = is_numeric($csv[$i][0]) ? null : $csv[$i][0];
                $raceEventEntries[] = [
                    'raceEventId' => $raceEvent->id,
                    'categoryId' => $raceCategoryId ?? null,
                    'raceEventParticipantId' => $eventParticipant->id,
                    'raceEventStageId' => $raceEventStage ? $raceEventStage->id : null,
                    'time' => $time,
                    'prependTime' => $prependTime,
                    'timeRaw' => $timeRaw,
                    'rank' => $status ? null : $rank,
                    'status' => $status,
                    'created_at' => \DB::raw('NOW()'),
                    'updated_at' => \DB::raw('NOW()'),
                ];
            }
        }

        \DB::table('race_event_entries')->insert($raceEventEntries);

        return back()->with([
            'success' => 'Successfully imported ' . count($raceEventEntries) . ' entries.'
        ]);
    }

    public function deleteAllResults(Request $request, Entry $entry, $categoryId, $stageId) {
        $builder = RaceEventEntry::where('raceEventId', $entry->id)
            ->where('categoryId', $categoryId);

        if ($stageId) {
            $builder->where('raceEventStageId', $stageId);
        }



        $builder->delete();

        RaceEventTeam::where('raceEventId', $entry->id)
            ->where('categoryId', $categoryId)
            ->delete();
        RaceEventParticipant::where('raceEventId', $entry->id)
            ->where('categoryId', $categoryId)
            ->delete();

        return back()->with([
            'success' => 'Entries were deleted.',
        ]);
    }

    public function delete(Request $request, Entry $entry) {
        $name = $entry->name;
        $entry->delete();

        return back()->with([
            'success' => 'Successfully deleted ' . $name . '.'
        ]);
    }
}
