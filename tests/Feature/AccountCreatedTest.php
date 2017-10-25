<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Aggregate;

class AccountCreatedTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateAccount()
    {
        $client_id = $this->json('POST', 'minibank/addClient', ['client_name' => 'Marcus'])->baseResponse->getContent();
        $aggregate_id = $this->json('POST', 'minibank/createAccount', ['client_id' => $client_id])->baseResponse->getContent();
        $eventCount = Aggregate::where('id', $aggregate_id)->first()->events()->count();
        $this->assertEquals(1, $eventCount);
    }
}
