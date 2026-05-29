<?php

namespace App\Application\Maintenance;

use App\Domain\Cashbox\CoinDenomination;
use App\Domain\Cashbox\MachineCashboxRepositoryInterface;
use App\Domain\Inventory\ProductRepositoryInterface;
use App\Domain\Inventory\ProductType;

class ServiceMachineUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly MachineCashboxRepositoryInterface $cashboxRepository
    ) {
    }

    public function execute(ServiceMachineCommand $command): void
    {
        $this->validateCoins($command->coins);
        $this->updateProducts($command->productsStock);
        $this->addCoinsToCashbox($command->coins);
    }

    private function validateCoins(array $coins): void
    {
        foreach (array_keys($coins) as $denominationValue) {
            if (!CoinDenomination::tryFrom((int) $denominationValue)) {
                throw new \InvalidArgumentException(sprintf('Invalid coin denomination: %d', $denominationValue));
            }
        }
    }

    private function updateProducts(array $productsStock): void
    {
        foreach ($productsStock as $typeName => $data) {
            $type = ProductType::tryFrom($typeName);
            if (!$type) {
                throw new \InvalidArgumentException(sprintf('Invalid product type: %s', $typeName));
            }

            $product = $this->productRepository->findByType($type);
            if (!$product) {
                throw new \RuntimeException(sprintf('Product %s not found in inventory.', $typeName));
            }

            if (is_array($data)) {
                if (isset($data['stock'])) {
                    $product->addStock((int)$data['stock']);
                }
                if (isset($data['price'])) {
                    $product->setPrice((int)$data['price']);
                }
            } else {
                // Backward compatibility for simple quantity input
                $product->addStock((int)$data);
            }

            $this->productRepository->save($product);
        }
    }

    private function addCoinsToCashbox(array $coins): void
    {
        if (empty($coins)) {
            return;
        }
        $this->cashboxRepository->addCoins($coins);
    }
}
