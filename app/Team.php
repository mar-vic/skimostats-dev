<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Adding HasFactory trait class
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Image;

class Team extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    private $imagesFolder = 'images/teams/';

    public function athletes() {
        return $this->hasMany('App\TeamAthlete', 'teamId');
    }

    public function country() {
        return $this->belongsTo('App\Country', 'countryId');
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
