<?php

namespace App;

use App\Aggregate;
use App\EventTypes;

class Account
{
	public $id;
	public $balance;

    public function __construct()
    {
        $argv = func_get_args();
        $argc = func_num_args();

        if($argc == 1)
            self::__construct1($argv[0]);
        else if($argc == 2)
            self::__construct2($argv[0], $argv[1]);
    }
 
    public function __construct1($aggregate_id) {
        $this->id = $aggregate_id;

        $aggregate = Aggregate::where('id', $aggregate_id)->first();
        $events = $aggregate->events()->getResults();

        foreach ($events as $event) {
            if( $event->type == EventTypes::getValue("ACCOUNT_CREATED") )
                $this->balance = $event->data;
            else if( $event->type == EventTypes::getValue("MONEY_DEPOSITED") )
                $this->balance += $event->data;
            else if( $event->type == EventTypes::getValue("MONEY_WITHDREW") )
                $this->balance -= $event->data;
        }
    }
 
    function __construct2($aggregate_id, $event_limit) {
        $this->id = $aggregate_id;

        $aggregate = Aggregate::where('id', $aggregate_id)->first();
        $events = $aggregate->events()->limit($event_limit)->getResults();

        foreach ($events as $event) {
            if( $event->type == EventTypes::getValue("ACCOUNT_CREATED") )
                $this->balance = $event->data;
            else if( $event->type == EventTypes::getValue("MONEY_DEPOSITED") )
                $this->balance += $event->data;
            else if( $event->type == EventTypes::getValue("MONEY_WITHDREW") )
                $this->balance -= $event->data;
        }
    }
}
