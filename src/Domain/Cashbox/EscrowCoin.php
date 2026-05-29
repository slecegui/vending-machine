<?php

declare(strict_types=1);

namespace App\Domain\Cashbox;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a single coin currently held in the machine's temporary deposit.
 */
#[ORM\Entity]
#[ORM\Table(name: 'escrow_coins')]
class EscrowCoin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', enumType: CoinDenomination::class)]
    private CoinDenomination $denomination;

    public function __construct(CoinDenomination $denomination)
    {
        $this->denomination = $denomination;
    }

    public function getDenomination(): CoinDenomination
    {
        return $this->denomination;
    }
}
