<?php

namespace App\Domain\Inventory;

enum ProductType: string
{
    case WATER = 'WATER';
    case JUICE = 'JUICE';
    case SODA = 'SODA';

    public function getPrice(): int
    {
        return match ($this) {
            self::WATER => 65,
            self::JUICE => 100,
            self::SODA => 150,
        };
    }
}
