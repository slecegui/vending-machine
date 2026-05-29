<?php

declare(strict_types=1);

namespace App\Application\Purchase;

use App\Domain\Cashbox\EscrowCoin;
use App\Domain\Cashbox\EscrowRepositoryInterface;

/**
 * Use Case to retrieve the current total credit in the machine's escrow.
 */
class GetEscrowCreditUseCase
{
    public function __construct(
        private readonly EscrowRepositoryInterface $escrowRepository
    ) {
    }

    public function execute(): int
    {
        $coins = $this->escrowRepository->findAll();
        
        return array_sum(array_map(fn(EscrowCoin $c) => $c->getDenomination()->value, $coins));
    }
}
