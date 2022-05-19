<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerCategory extends Model
{
    protected $guarded = ['id'];

    public function getEntriesAttribute() {
        return $this->partners ? json_decode($this->partners) : [];
    }

    public function getImagePathAttribute() {
        return public_path('uploads/'.$this->image);
    }
}
