<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\EventStore;
use App\Account;

class Commands extends Controller
{
    public function createAccount(Request $request)
    {
        $client_id = json_decode($request->getContent(), true)['client_id'];

        $client = Client::where('id', $client_id)->first();

        $aggregate = $request->get('aggregate');
        $event = $request->get('event');

        $client->aggregates()->save($aggregate);

        EventStore::save($aggregate, $event);

        return $aggregate->id;
    }

    public function depositMoney(Request $request)
    {
        $aggregate = $request->get('aggregate');
        $event = $request->get('event');

        EventStore::save($aggregate, $event);

        $account = new Account($aggregate->id);

        return ['new_event' => $event, 'new_balance' => $account->balance];
    }

    public function withdrawMoney(Request $request)
    {
        $exception = $request->get('exception');

        if($exception != null)
            return $exception;
        else {
            $aggregate = $request->get('aggregate');
            $event = $request->get('event');

            EventStore::save($aggregate, $event);

            $account = new Account($aggregate->id);

            return ['new_event' => $event, 'new_balance' => $account->balance];
        }        
    }
}