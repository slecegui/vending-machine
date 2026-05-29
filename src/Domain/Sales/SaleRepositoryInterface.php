<?php

declare(strict_types=1);

namespace App\Domain\Sales;

/**
 * Interface for Sale persistence.
 */
interface SaleRepositoryInterface
{
    public function save(Sale $sale): void;
}
