<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{	
	public $timestamps = false;
	
    public function accounts()
    {
        return $this->hasMany('App\Events');
    }
}
