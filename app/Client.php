<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
	protected $primaryKey = 'cpf';
    public $incrementing = false;

    public $timestamps = false;
	
    protected $fillable = [
        'name', 'cpf'
    ];

    public function accounts()
    {
        return $this->hasMany('App\Accounts');
    }
}
