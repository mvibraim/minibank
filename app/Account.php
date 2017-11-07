<?php

namespace App;

use App\Aggregate;
use App\EventTypes;
use App\EventStore;
use App\Event;

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
 
    public function __construct2($aggregate_id, $event_limit) {
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

    public function create($client, $aggregate) {
        $client->aggregates()->save($aggregate);
        $event = new Event(['type' => EventTypes::getValue('ACCOUNT_CREATED'), 'data' => 0]);
        EventStore::save($aggregate, $event);
    }

    public function deposit($aggregate, $amount) {
        $event = new Event(['type' => EventTypes::getValue('MONEY_DEPOSITED'), 'data' => $amount]);
        EventStore::save($aggregate, $event);
        return $event;
    }

    public function withdraw($aggregate, $amount) {
        $event = new Event(['type' => EventTypes::getValue('MONEY_WITHDREW'), 'data' => $amount]);
        EventStore::save($aggregate, $event);
        return $event;
    }
}
