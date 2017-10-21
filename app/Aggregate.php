<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aggregate extends Model
{	
	public $timestamps = false;
	
    public function events()
    {
        return $this->hasMany('App\Event');
    }
}
