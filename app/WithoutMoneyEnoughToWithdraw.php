<?php

namespace App;

class WithoutMoneyEnoughToWithdraw
{
	public $message = "Without Money Enough To Withdraw";

	public function __toString()
    {
        return $this->message;
    }
}