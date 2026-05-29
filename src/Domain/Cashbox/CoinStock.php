<?php

namespace App\Domain\Cashbox;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'coin_stock')]
class CoinStock
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', enumType: CoinDenomination::class)]
    private CoinDenomination $denomination;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function __construct(CoinDenomination $denomination, int $quantity)
    {
        $this->denomination = $denomination;
        $this->quantity = $quantity;
    }

    public function getDenomination(): CoinDenomination
    {
        return $this->denomination;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function addQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity to add cannot be negative.');
        }
        $this->quantity += $quantity;
    }
}
