<?php

namespace App;
use Cviebrock\EloquentSluggable\Sluggable;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Image;
use Carbon\Carbon;

use App\Ranking;
use App\Enums\RankingType;
use App\Http\Controllers\RankingController;
use App\RankingTable;
use Illuminate\Support\Facades\DB;

class Athlete extends Model
{
    use HasFactory, Sluggable;  // Added HasFactory for testing purposes

    protected $key = 'slug';
    protected $guarded = ['id'];

    private $imagesFolder = 'images/athletes/';

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['firstName', 'lastName'],
            ],
        ];
    }

    public function getIsmfPoints() {
        return Ranking::where('athleteId', $this->id)
            ->where('type', RankingType::ISMF)
            ->whereBetween('obtainedAt', ['2019-01-01 00:00:00', '2019-12-31 23:59:59']) // 2019 year
            ->where('categoryId', $this->getRankingCategory())
            ->sum('points');
    }

    public function getRankingCategory() {
        return $this->gender == 'male' ? 1 : 2;
    }

    public function getRankAttribute() {
        $rank = RankingTable::where('athleteId', $this->id)
            ->where('type', RankingType::SKIMO_STATS)
            ->where('year', RankingController::getActualYear())
            ->orderBy('rank', 'asc')
            ->first();

        if (!$rank) {
            return false;
        }

        $cat = Category::where('id', $rank->categoryId)->first();

        return (object)[
            'rank' => $rank->rank,
            'points' => $rank->points,
            'url' => route('rankings.category', ['skimostats', $cat->slug])."#athlete-rank-".$this->slug
        ];
    }

    public function getTotalPoints() {
        $totalPoints = DB::table('ranking_tables as r')
            ->select(
                'r.points',
                'r.athleteId',
                'r.rank',
                'a.firstName',
                'a.lastName',
                'a.slug as athleteSlug',
                'c.code as countryCode'
            )
            ->join('athletes as a', 'a.id', 'r.athleteId')
            ->leftJoin('countries as c', 'c.id', 'a.countryId')
            ->where('r.type', RankingType::SKIMO_STATS)
            ->where('r.athleteId', $this->id)
            // ->where('r.categoryId', $category->id)
            ->sum('r.points');

        return (int)$totalPoints;
    }

    public function getVictoriesCount() {
        $count = DB::table('race_events as re')
            ->select('re.id')
            ->join('race_event_participants as rep', 'rep.raceEventId', 're.id')
            ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
            ->leftJoin('race_events as re2', 're.parent', 're2.id')
            ->leftJoin('race_events as re3', 're2.parent', 're3.id')
            ->leftJoin('race_events as re4', 're3.parent', 're4.id')
            ->leftJoin('race_events as re5', 're4.parent', 're5.id')
            ->join(
                'race_event_entries as ree',
                function($join) {
                    $join->on('ree.raceEventId', 're.id');
                    $join->on(function($j){
                        $j->orOn('ree.raceEventParticipantId','rep.id');
                        $j->orOn('ree.raceEventTeamId','ret.id');
                    });
                }
            )
            ->where('ree.rank', 1)
            ->where('rep.athleteId', $this->id)
            ->whereIn(
                DB::raw('COALESCE(re.rankingCategoryId, re2.rankingCategoryId, re3.rankingCategoryId, re4.rankingCategoryId, re5.rankingCategoryId)'),
                [1,2,5,6,7]
            )->count();

        // $builder = DB::table('race_events as re')
        //     ->select(
        //         're.startDate as eventStartDate',
        //         're.name as eventName',
        //         're.slug as eventSlug',
        //         'rt.name as raceType',
        //         'rnk.rank',
        //         'rnk.points',
        //         'cat.slug as categorySlug',
        //         'c.name as countryName',
        //         'c.code as countryCode'
        //     )
        //     ->join('race_event_participants as rep', 'rep.raceEventId', 're.id')
        //     ->join('categories as cat', 'cat.id', 'rep.categoryId')
        //     ->leftJoin('countries as c', 'c.id', 're.countryId')
        //     ->leftJoin('race_event_teams as ret', 'ret.id', 'rep.raceEventTeamId')
        //     ->join('race_event_entries as ree', function($qb) {
        //         $qb->on('ree.raceEventParticipantId', '=', 'rep.id')
        //             ->orOn('ree.raceEventTeamId', '=', 'ret.id');
        //     })
        //     ->leftJoin('rankings as rnk', function($qb) {
        //         $qb->on('rnk.participantId', '=', 'rep.id')
        //             ->where('rnk.type', 1)
        //             ->whereIn('rnk.categoryId', [1, 2]);
        //     })
        //     ->leftJoin('race_types as rt', 'rt.id', 're.type')
        //     ->where('rep.athleteId', $this->id)
        //     ->where('ree.rank', 1)
        //     ->whereRaw('rnk.rankingCategoryId in (1, 2, 4, 5, 6, 7, 8, 9, 10, 13)')
        //     ->groupBy('re.id');

        // dd($builder);
        // error_log($this->firstName . $this->lastName . ': ' . $builder->get->());

        return 1000000;
    }


    public function getIsmfRank() {
        $rank = DB::table('rankings as r')
            ->select(DB::raw('SUM(r.points) as pts'), 'r.athleteId')
            ->where('r.athleteId', '!=', $this->id)
            ->where('r.type', RankingType::ISMF)
            ->whereBetween('r.obtainedAt', ['2019-01-01 00:00:00', '2019-12-31 23:59:59']) // 2019 year
            ->where('r.categoryId', $this->getRankingCategory())
            ->groupBy('r.athleteId')
            ->havingRaw('pts > ?', [$this->getIsmfPoints()])
            ->get();
        return count($rank) + 1;
    }

    public function getNameAttribute() {
        return $this->firstName . " " . $this->lastName;
    }

    public function country() {
        return $this->belongsTo('App\Country', 'countryId');
    }

    public function getImagePathAttribute() {
        return '/' . $this->imagesFolder . $this->image;
    }

    public function scopeWithFullName($query, $firstName, $lastName) {
        return $query->where('firstName', 'LIKE', $firstName)->where('lastName', 'LIKE', $lastName);
    }

    public function getImageUrl() {
        return $this->image ? '/images/athletes/'.$this->image : $this->getSampleImageUrl();
    }

    public function getAvatarAttribute() {
        return $this->getImageUrl();
    }

    public function getSampleImageUrl() {
        return $this->gender === 'female' ? '/images/woman_silhouette.jpg' : '/images/man_silhouette.jpg';
    }

    public static function getAthleteImageUrl($image, $gender) {
        if ($image) {
            return '/images/athletes/' . $image;
        }
        return $gender === 'female' ? '/images/woman_silhouette.jpg' : '/images/man_silhouette.jpg';
    }

    public function getSocialLinksArrayAttribute() {
        return $this->socialLinks ? json_decode($this->socialLinks) : [];
    }

    public function hasSocialLink($which) {
        return isset($this->socialLinksArray->{$which});
    }

    public function getSocialLink($which) {
        if ($this->hasSocialLink($which)) {
            return $this->socialLinksArray->{$which};
        }
        return null;
    }

    public function dateOfBirthFormatted() {
        return (new Carbon($this->dateOfBirth))->isoFormat('Do MMMM YYYY');
    }

    public function getAgeAttribute() {
        return Carbon::now()->diffInYears(new Carbon($this->dateOfBirth));
    }

    public function topResults() {
        return $this->hasMany('App\AthleteTopResult', 'athleteId')->orderBy('position', 'asc');
    }

    public function logVisit() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $this->clicks++;
        $this->save();

        $hasVisit = AthleteVisit::where('athleteId', $this->id)->where('ipAddress', $ip)->where('userAgent', $user_agent)->first();
        if (!$hasVisit) {
            $newVisit = new AthleteVisit();
            $newVisit->ipAddress = $ip;
            $newVisit->userAgent = $user_agent;
            $newVisit->athleteId = $this->id;
            $newVisit->lastVisit = Carbon::now();
            $newVisit->save();
        } else {
            $hasVisit->lastVisit = Carbon::now();
            $hasVisit->save();
        }
    }

    public function getWeeklyVisitsAttribute() {
        return AthleteVisit::where('athleteId', $this->id)->where('lastVisit', '>', Carbon::now()->subDays(7))->count();
    }

    public function getLastWeekVisitsAttribute() {
        return AthleteVisit::where('athleteId', $this->id)
            ->where('lastVisit', '<=', Carbon::now()->subDays(7))
            ->where('lastVisit', '>', Carbon::now()->subDays(14))
            ->count();
    }

    public function getWeeklyVisitsRisingAttribute() {
        $thisWeek = $this->weekly_visits;
        $lastWeek = $this->last_week_visits;

        return $thisWeek >= $lastWeek;
    }

    public function setImage($image) {
        $imageName = $this->id . "-" . uniqid() . '.' . $image->getClientOriginalExtension();
        $img = Image::make($image)->resize(600, 600, function($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        if (!is_dir(public_path($this->imagesFolder))) {
            mkdir(public_path($this->imagesFolder));
        }

        $path = $this->imagesFolder . $imageName;
        $img->save(public_path($path));

        if ($this->image && file_exists(public_path($this->imagesFolder . $this->image))) {
            unlink(public_path($this->imagesFolder . $this->image));
        }

        $this->image = $imageName;
        $this->save();
    }

    // *********************
    // * NEW STUFF BY MV   *
    // *********************

    public function getNumberOfCareerWins()
    {
        // returns the number of all wins of an athlete in their whole career
        // where win is a rank 1 position in a race event
        // and only races in the following categories are considered: 1, 2, 4, 5, 6, 7, 8, 9, 10, 13
        return Ranking::where('athleteId', '=', "{$this->id}")
            ->where('rank', '=', 1)
            ->whereRaw('rankingCategoryId in (1, 2, 4, 5, 6, 7, 8, 9, 10, 13)')
            ->count();
    }

    public function getCareerWins()
    {
        // return all career wins of an athlete (ie, a collection of triplets <eventId, eventYear, eventName>)
        // where win is a rank 1 position on a race event
        // and only race events in the following categories are considered: 1, 2, 4, 5, 6, 7, 8, 9, 10, 13
        return Ranking::where('athleteId', '=', "{$this->id}")
            ->join('race_events as events', 'events.id', '=', 'rankings.raceEventId')
            ->where('rankings.rank', '=', 1)
            ->whereRaw('rankings.rankingCategoryId in (1, 2, 4, 5, 6, 7, 8, 9, 10, 13)')
            ->select('events.id', 'events.year', 'events.name')
            ->orderBy('events.year', 'desc')
            ->get();
    }

    public function getTopCareerResults() {

    }
}
