<?php

namespace App\Http\Middleware;

use Closure;
use App\Aggregate;
use App\Event;
use App\EventTypes;
use App\Account;
use App\WithoutMoneyEnoughToWithdraw;

class CommandHandler
{
    public function handle($request, Closure $next)
    {
        if( strpos($request->path(), "createAccount") ) {
            $aggregate = new Aggregate;
            $event = new Event(['type' => EventTypes::getValue('ACCOUNT_CREATED'), 'data' => 0]);

            $request->attributes->set('aggregate', $aggregate);
            $request->attributes->set('event', $event);

            return $next($request);
        }
        else if( strpos($request->path(), "depositMoney") ) {
            $account_id = json_decode($request->getContent(), true)['account_id'];
            $amount = json_decode($request->getContent(), true)['amount'];

            $aggregate = Aggregate::where('id', $account_id)->first();
            $event = new Event(['type' => EventTypes::getValue('MONEY_DEPOSITED'), 'data' => $amount]);

            $request->attributes->set('aggregate', $aggregate);
            $request->attributes->set('event', $event);

            return $next($request);
        }
        else if( strpos($request->path(), "withdrawMoney") ) {
            $account_id = json_decode($request->getContent(), true)['account_id'];
            $amount = json_decode($request->getContent(), true)['amount'];

            $account = new Account($account_id);

            if($account->balance - $amount < 0)
                $request->attributes->set('exception', new WithoutMoneyEnoughToWithdraw);
            else {
                $aggregate = Aggregate::where('id', $account_id)->first();
                $event = new Event(['type' => EventTypes::getValue('MONEY_WITHDREW'), 'data' => $amount]);

                $request->attributes->set('aggregate', $aggregate);
                $request->attributes->set('event', $event);
            }

            return $next($request);
        }
    }
}
