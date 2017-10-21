<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $timestamps = false;
	
    protected $fillable = [
        'name'
    ];

    public function aggregates()
    {
        return $this->hasMany('App\Aggregate');
    }
}
