<?php

declare(strict_types=1);

namespace App\Application\Purchase;

use App\Domain\Cashbox\EscrowCoin;
use App\Domain\Cashbox\EscrowRepositoryInterface;
use App\Domain\Cashbox\MachineCashboxRepositoryInterface;

class RefundUseCase
{
    public function __construct(
        private readonly MachineCashboxRepositoryInterface $cashboxRepository,
        private readonly EscrowRepositoryInterface $escrowRepository
    ) {
    }

    /**
     * @return int[] The physical coins returned to the user
     */
    public function execute(): array
    {
        $escrowCoins = $this->escrowRepository->findAll();
        if (empty($escrowCoins)) {
            return [];
        }

        $refundMap = [];
        $returnedValues = [];
        foreach ($escrowCoins as $coin) {
            $value = $coin->getDenomination()->value;
            $refundMap[$value] = ($refundMap[$value] ?? 0) + 1;
            $returnedValues[] = $value;
        }

        // 1. Physically subtract the escrowed coins from the internal stock
        $this->cashboxRepository->subtractCoins($refundMap);

        // 2. Clear user credit
        $this->escrowRepository->clear();

        return $returnedValues;
    }
}
