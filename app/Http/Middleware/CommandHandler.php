<?php

namespace App\Http\Middleware;

use Closure;
use App\Aggregate;
use App\Account;
use App\WithoutMoneyEnoughToWithdraw;
use DB;

class CommandHandler
{
    public function handle($request, Closure $next)
    {
        if( strpos($request->path(), "createAccount") ) {
            $aggregate = new Aggregate;
            $request->attributes->set('aggregate', $aggregate);

            return $next($request);
        }
        else if( strpos($request->path(), "depositMoney") ) {
            $account_id = json_decode($request->getContent(), true)['account_id'];
            $aggregate = Aggregate::where('id', $account_id)->first();
            $request->attributes->set('aggregate', $aggregate);

            return $next($request);
        }
        else if( strpos($request->path(), "withdrawMoney") ) {
            $account_id = json_decode($request->getContent(), true)['account_id'];
            $amount = json_decode($request->getContent(), true)['amount'];

            DB::transaction(function() use ($request, $account_id, $amount)
            {
                $account = new Account($account_id);

                if($account->balance - $amount < 0)
                    $request->attributes->set('exception', new WithoutMoneyEnoughToWithdraw);
                else {
                    $aggregate = Aggregate::where('id', $account_id)->first();
                    $request->attributes->set('aggregate', $aggregate);
                }
            });

            return $next($request);
        }
    }
}
