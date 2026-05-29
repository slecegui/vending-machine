<?php

namespace App\Domain\Cashbox;

interface MachineCashboxRepositoryInterface
{
    /**
     * @param array<int, int> $coins Map of denomination (cents) to quantity
     */
    public function addCoins(array $coins): void;

    /**
     * @param array<int, int> $coins Map of denomination (cents) to quantity
     */
    public function subtractCoins(array $coins): void;

    public function findByDenomination(CoinDenomination $denomination): ?CoinStock;

    /**
     * @return CoinStock[]
     */
    public function findAll(): array;
}
