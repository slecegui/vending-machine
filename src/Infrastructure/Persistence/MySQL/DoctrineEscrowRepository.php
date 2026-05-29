<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySQL;

use App\Domain\Cashbox\EscrowCoin;
use App\Domain\Cashbox\EscrowRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EscrowCoin>
 */
class DoctrineEscrowRepository extends ServiceEntityRepository implements EscrowRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EscrowCoin::class);
    }

    public function add(EscrowCoin $coin): void
    {
        $this->getEntityManager()->persist($coin);
        $this->getEntityManager()->flush();
    }

    public function clear(): void
    {
        $this->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
