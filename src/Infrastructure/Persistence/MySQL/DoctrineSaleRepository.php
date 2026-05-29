<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySQL;

use App\Domain\Sales\Sale;
use App\Domain\Sales\SaleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class DoctrineSaleRepository extends ServiceEntityRepository implements SaleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    public function save(Sale $sale): void
    {
        $this->getEntityManager()->persist($sale);
        $this->getEntityManager()->flush();
    }
}
