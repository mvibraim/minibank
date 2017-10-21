<?php

namespace App;

use WBoyz\LaravelEnum\BaseEnum;

class EventTypes extends BaseEnum
{
    const ACCOUNT_CREATED = 0;
    const MONEY_DEPOSITED = 1;
    const MONEY_WITHDREW = 2;
}