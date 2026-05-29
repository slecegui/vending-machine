<?php

declare(strict_types=1);

namespace App\Application\Purchase;

use App\Domain\Cashbox\CoinDenomination;
use App\Domain\Cashbox\EscrowCoin;
use App\Domain\Cashbox\EscrowRepositoryInterface;
use App\Domain\Cashbox\MachineCashboxRepositoryInterface;

class InsertCoinUseCase
{
    public function __construct(
        private readonly MachineCashboxRepositoryInterface $cashboxRepository,
        private readonly EscrowRepositoryInterface $escrowRepository
    ) {
    }

    public function execute(int $coinValue): int
    {
        $denomination = CoinDenomination::tryFrom($coinValue);
        if (!$denomination) {
            throw new \InvalidArgumentException(sprintf('Invalid coin denomination: %d', $coinValue));
        }

        // 1. Physically add to the machine's internal stock
        $this->cashboxRepository->addCoins([$denomination->value => 1]);

        // 2. Add to the temporary escrow (user credit)
        $this->escrowRepository->add(new EscrowCoin($denomination));

        // Return current total credit
        $escrowCoins = $this->escrowRepository->findAll();
        return array_sum(array_map(fn(EscrowCoin $c) => $c->getDenomination()->value, $escrowCoins));
    }
}
