<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Aggregate;
use App\Account;

class AccountTest extends TestCase
{
	use DatabaseMigrations;

    public function testAccountBalance()
    {
        $client_id = $this->json('POST', 'minibank/addClient', ['client_name' => 'Marcus'])->baseResponse->getContent();
        $aggregate_id = $this->json('POST', 'minibank/createAccount', ['client_id' => $client_id])->baseResponse->getContent();
        $this->json('POST', 'minibank/depositMoney', ['account_id' => $aggregate_id, 'amount' => 10]);
        $this->json('POST', 'minibank/withdrawMoney', ['account_id' => $aggregate_id, 'amount' => 5]);
        $account = new Account($aggregate_id);

        $this->assertEquals($account->balance, 5);
    }
}
