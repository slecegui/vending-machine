<?php

declare(strict_types=1);

namespace App\Domain\Cashbox;

use App\Domain\Cashbox\Exception\NotEnoughChangeException;

/**
 * Domain service to calculate and update coin stock for change.
 */
class ChangeCalculatorService
{
    public function __construct(
        private readonly MachineCashboxRepositoryInterface $cashboxRepository
    ) {
    }

    /**
     * Calculates the change using a greedy algorithm and subtracts it from the physical stock.
     * 
     * @param int $changeRequired Total amount of change to return in cents
     * @return int[] Array of change coin denominations
     * @throws NotEnoughChangeException
     */
    public function calculateAndSubtractChange(int $changeRequired): array
    {
        if ($changeRequired === 0) {
            return [];
        }

        $currentStock = $this->cashboxRepository->findAll();
        $stockMap = [];
        foreach ($currentStock as $coinStock) {
            $stockMap[$coinStock->getDenomination()->value] = $coinStock->getQuantity();
        }

        $denominations = array_map(fn(CoinDenomination $d) => $d->value, CoinDenomination::cases());
        rsort($denominations);

        $changeCoins = [];
        $remainingChange = $changeRequired;

        foreach ($denominations as $coinValue) {
            while ($remainingChange >= $coinValue && ($stockMap[$coinValue] ?? 0) > 0) {
                $changeCoins[] = $coinValue;
                $remainingChange -= $coinValue;
                $stockMap[$coinValue]--;
            }
        }

        if ($remainingChange > 0) {
            throw new NotEnoughChangeException();
        }

        $changeMap = [];
        foreach ($changeCoins as $coin) {
            $changeMap[$coin] = ($changeMap[$coin] ?? 0) + 1;
        }
        $this->cashboxRepository->subtractCoins($changeMap);

        return $changeCoins;
    }
}
