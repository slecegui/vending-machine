<?php

namespace App\Domain\Cashbox;

interface MachineCashboxRepositoryInterface
{
    /**
     * @param array<int, int> $coins Map of denomination (cents) to quantity
     */
    public function addCoins(array $coins): void;

    /**
     * @return CoinStock[]
     */
    public function findAll(): array;
}
