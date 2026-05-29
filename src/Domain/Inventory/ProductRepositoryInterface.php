<?php

namespace App\Domain\Inventory;

interface ProductRepositoryInterface
{
    public function findByType(ProductType $type): ?Product;
    /** @return Product[] */
    public function findAll(): array;
    public function save(Product $product): void;
}
