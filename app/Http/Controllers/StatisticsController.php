<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function mostWins(Request $request) {
        $queryBuilder = DB::table('athletes')
                ->selectRaw('athletes.firstName, athletes.lastName, count(rankings.id) as numberOfWins')
                ->join('rankings', 'rankings.athleteId', 'athletes.id')
                ->where('rankings.rank', 1)
                ->groupBy('athletes.id')
                ->orderBy('numberOfWins', 'desc');

        return $queryBuilder->get();
    }

    public function mostWinsWithCategories(Request $request) {
        $queryBuilder = DB::table('athletes')
            ->selectRaw('athletes.id as athleteId, athletes.firstName, athletes.lastName, categories.name as catName')
            ->join('rankings', 'rankings.athleteId', 'athletes.id')
            ->join('categories', 'categories.id', 'rankings.categoryId')
            ->where('rankings.rank', 1);

        $groupedByCategories = $queryBuilder->get()->groupBy('catName');

        $groupedByAthletes = $groupedByCategories->map(function ($item, $key) {
            // dd($item);
            return $item->groupBy('athleteId')->map(function ($item, $key) {
                // dd($item->count());
                // dd($item[0]->firstName);
                return collect([
                    'athleteId' => $key,
                    'firsName' => $item[0]->firstName,
                    'lastName' => $item[0]->lastName,
                    'winsCount' => $item->count()
                ]);
            })->sortBy([['winsCount', 'desc']]);
        });

        return $groupedByAthletes;
    }
}
