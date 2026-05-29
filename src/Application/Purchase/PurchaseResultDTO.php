<?php

declare(strict_types=1);

namespace App\Application\Purchase;

/**
 * Result DTO for a successful purchase.
 */
readonly class PurchaseResultDTO
{
    /**
     * @param string $productName
     * @param int[] $changeCoins
     */
    public function __construct(
        public string $productName,
        public array $changeCoins
    ) {
    }
}
