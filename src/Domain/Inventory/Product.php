<?php

namespace App\Domain\Inventory;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 20, enumType: ProductType::class)]
    private ProductType $type;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    #[ORM\Column(type: 'integer')]
    private int $price;

    public function __construct(ProductType $type, int $stock, int $price)
    {
        $this->type = $type;
        $this->stock = $stock;
        $this->price = $price;
    }

    public function getType(): ProductType
    {
        return $this->type;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        if ($stock < 0) {
            throw new \InvalidArgumentException('Stock cannot be negative.');
        }
        $this->stock = $stock;
    }

    public function addStock(int $quantity): void
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity to add cannot be negative.');
        }
        $this->stock += $quantity;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Price cannot be negative.');
        }
        $this->price = $price;
    }
}
