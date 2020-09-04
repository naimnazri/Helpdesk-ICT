<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bahagian extends Model
{
    protected $guarded = [];

    public function bahagian(){

        return $this->hasMany('App\Bahagian', 'idjab');

    }
}
