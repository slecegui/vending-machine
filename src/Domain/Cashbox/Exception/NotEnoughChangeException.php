<?php

declare(strict_types=1);

namespace App\Domain\Cashbox\Exception;

/**
 * Exception thrown when the machine cannot return the exact change required.
 */
class NotEnoughChangeException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Not enough coins in the machine to provide exact change.');
    }
}
