<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\Account;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailAccountCreated;

class Commands extends Controller
{
    public function createAccount(Request $request)
    {
        $client_id = json_decode($request->getContent(), true)['client_id'];
        $client = Client::where('id', $client_id)->first();

        $aggregate = $request->get('aggregate');

        (new Account())->create($client, $aggregate);

        return $aggregate->id;
    }

    public function depositMoney(Request $request)
    {
        $amount = json_decode($request->getContent(), true)['amount'];
        $aggregate = $request->get('aggregate');

        $new_event = (new Account())->deposit($aggregate, $amount);

        $account = new Account($aggregate->id);

        return ['new_event' => $new_event, 'new_balance' => $account->balance];
    }

    public function withdrawMoney(Request $request)
    {
        $exception = $request->get('exception');

        if($exception != null)
            return $exception;
        else {
            $amount = json_decode($request->getContent(), true)['amount'];
            $aggregate = $request->get('aggregate');

            $new_event = (new Account())->withdraw($aggregate, $amount);

            $account = new Account($aggregate->id);

            return ['new_event' => $new_event, 'new_balance' => $account->balance];
        }        
    }

    public function sendEmail(Request $request)
    {
        $client_name = json_decode($request->getContent(), true)['client_name'];
        $cfo_email = 'cfo_email@minibank.com';
        Mail::to($cfo_email)->send(new EmailAccountCreated($client_name));     
    }
}