<?php

declare(strict_types=1);

namespace App\Domain\Sales\Exception;

/**
 * Exception thrown when the inserted coins sum is less than the product price.
 */
class InsufficientFundsException extends \DomainException
{
    public function __construct(int $required, int $provided)
    {
        parent::__construct(sprintf('Insufficient funds. Required: %d, Provided: %d', $required, $provided));
    }
}
