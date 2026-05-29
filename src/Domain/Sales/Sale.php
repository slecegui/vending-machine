<?php

declare(strict_types=1);

namespace App\Domain\Sales;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a completed sales transaction for accounting purposes.
 */
#[ORM\Entity]
#[ORM\Table(name: 'sales')]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $productName;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $productName, int $price)
    {
        $this->productName = $productName;
        $this->price = $price;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
