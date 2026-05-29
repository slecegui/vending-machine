<?php

namespace App\Domain\Cashbox;

enum CoinDenomination: int
{
    case FIVE = 5;
    case TEN = 10;
    case TWENTY_FIVE = 25;
    case ONE_HUNDRED = 100;
}
