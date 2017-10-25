<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Aggregate;
use App\Event;

class MoneyWithdrewTest extends TestCase
{
    use DatabaseMigrations;

    public function testWithdrawMoney()
    {
        $client_id = $this->json('POST', 'minibank/addClient', ['client_name' => 'Marcus'])->baseResponse->getContent();
        $aggregate_id = $this->json('POST', 'minibank/createAccount', ['client_id' => $client_id])->baseResponse->getContent();
        $this->json('POST', 'minibank/depositMoney', ['account_id' => $aggregate_id, 'amount' => 10]);
        $this->json('POST', 'minibank/withdrawMoney', ['account_id' => $aggregate_id, 'amount' => 5]);

        $eventCount = Aggregate::where('id', $aggregate_id)->first()->events()->count();

        $eventType = Event::where('aggregate_id', $aggregate_id)->orderBy('id', 'DESC')->first()->type;

        $this->assertTrue($eventCount == 3 && $eventType == 2);
    }
}
