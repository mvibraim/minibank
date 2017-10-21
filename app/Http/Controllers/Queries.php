<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\Account;
use App\Aggregate;

class Queries extends Controller
{
    public function getAccounts($client_id)
    {
        $client = Client::where('id', $client_id)->first();
        $aggregates = $client->aggregates()->getResults();
        $accounts = array();
        $event_counts = array();

        foreach ($aggregates as $aggregate) {
        	$account = new Account($aggregate->id);
        	array_push($accounts, $account);
            array_push($event_counts, $aggregate->events()->count());
        }

        return ['accounts' => $accounts, 'event_counts' => $event_counts];
    }

    public function getEvents($account_id)
    {
        $aggregate = Aggregate::where('id', $account_id)->first();
        $events = $aggregate->events()->getResults();
        return $events;
    }

    public function replay($account_id, $event_limit)
    {
        $aggregate = Aggregate::where('id', $account_id)->first();
        $account = new Account($aggregate->id, $event_limit);
        return $account->balance;
    }
}