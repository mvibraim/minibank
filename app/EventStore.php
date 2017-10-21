<?php

namespace App;

class EventStore
{
    public static function save($aggregate, $event)
    {
        $aggregate->events()->save($event);
    }
}
