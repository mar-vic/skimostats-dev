<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Image;

class Race extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

    protected $casts = [
        'yearStart' => 'date',
        'yearEnd' => 'date',
    ];

    private $imagesFolder = 'images/races/';

    private static $ismfWorldCupRaces = [7]; //, 10, 14];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public static function getIsmfWorldCupIds() {
        return self::$ismfWorldCupRaces;
    }

    public function raceCategories() {
        return $this->hasMany('App\RaceCategory', 'raceId');
    }

    public function raceTypes() {
        return $this->hasMany('App\RaceType', 'raceId');
    }

    public function rankingCategory() {
        return $this->belongsTo('App\RankingCategory', 'rankingCategoryId');
    }

    public function getImagePathAttribute() {
        return '/' . $this->imagesFolder . $this->image;
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
}
