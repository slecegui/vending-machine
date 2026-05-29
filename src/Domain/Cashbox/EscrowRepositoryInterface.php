<?php

declare(strict_types=1);

namespace App\Domain\Cashbox;

interface EscrowRepositoryInterface
{
    public function add(EscrowCoin $coin): void;

    /** @return EscrowCoin[] */
    public function findAll(): array;

    public function clear(): void;
}
