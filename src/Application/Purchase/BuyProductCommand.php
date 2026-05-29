<?php

declare(strict_types=1);

namespace App\Application\Purchase;

/**
 * DTO for the Buy Product intent.
 */
readonly class BuyProductCommand
{
    public function __construct(
        public string $productName
    ) {
    }
}
