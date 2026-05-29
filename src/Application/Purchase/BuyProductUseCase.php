<?php

declare(strict_types=1);

namespace App\Application\Purchase;

use App\Domain\Cashbox\ChangeCalculatorService;
use App\Domain\Cashbox\EscrowCoin;
use App\Domain\Cashbox\EscrowRepositoryInterface;
use App\Domain\Inventory\Exception\ProductOutOfStockException;
use App\Domain\Inventory\ProductRepositoryInterface;
use App\Domain\Inventory\ProductType;
use App\Domain\Sales\Exception\InsufficientFundsException;
use App\Domain\Sales\Sale;
use App\Domain\Sales\SaleRepositoryInterface;

class BuyProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly EscrowRepositoryInterface $escrowRepository,
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly ChangeCalculatorService $changeCalculator
    ) {
    }

    public function execute(BuyProductCommand $command): PurchaseResultDTO
    {
        // 1. Get Escrow Balance
        $escrowCoins = $this->escrowRepository->findAll();
        $insertedTotal = array_sum(array_map(fn(EscrowCoin $c) => $c->getDenomination()->value, $escrowCoins));

        // 2. Validate Product
        $type = ProductType::tryFrom($command->productName);
        if (!$type) {
            throw new \InvalidArgumentException(sprintf('Invalid product: %s', $command->productName));
        }

        $product = $this->productRepository->findByType($type);
        if (!$product || $product->getStock() <= 0) {
            throw new ProductOutOfStockException($command->productName);
        }

        // 3. Validate Funds
        if ($insertedTotal < $product->getPrice()) {
            throw new InsufficientFundsException($product->getPrice(), $insertedTotal);
        }

        // 4. Handle Change (Physical side)
        $changeRequired = $insertedTotal - $product->getPrice();
        $changeCoins = $this->changeCalculator->calculateAndSubtractChange($changeRequired);

        // 5. Update Inventory (Physical side)
        $product->setStock($product->getStock() - 1);
        $this->productRepository->save($product);

        // 6. Record the Sale (Accounting side)
        $this->saleRepository->save(new Sale($product->getType()->value, $product->getPrice()));

        // 7. Clear Escrow (Credit side)
        $this->escrowRepository->clear();

        return new PurchaseResultDTO($product->getType()->value, $changeCoins);
    }
}
