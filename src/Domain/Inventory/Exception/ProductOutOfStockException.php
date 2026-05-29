<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Exception;

/**
 * Exception thrown when a product is requested but its stock is zero.
 */
class ProductOutOfStockException extends \DomainException
{
    public function __construct(string $productName)
    {
        parent::__construct(sprintf('Product "%s" is out of stock.', $productName));
    }
}
